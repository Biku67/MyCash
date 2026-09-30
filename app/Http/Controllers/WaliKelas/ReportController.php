<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\TransaksiKas;
use App\Models\Kategori;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Exports\BendaharaReportExport;
use App\Exports\BendaharaStudentExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ReportController extends Controller
{
    /**
     * Get the authenticated Wali Kelas record.
     */
    private function getWaliKelas()
    {
        $waliKelas = auth()->user()->waliKelas;
        if (!$waliKelas) {
            abort(403, 'Akses ditolak. Anda belum terdaftar sebagai wali kelas.');
        }
        return $waliKelas;
    }

    /**
     * Display the financial report and cash flow summary for Wali Kelas.
     */
    public function index(Request $request)
    {
        $request->validate([
            'tab'        => 'nullable|in:buku_kas,tunggakan',
            'kode_kelas' => 'nullable|string',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date'   => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'category_id'=> 'nullable|exists:kategori,id',
            'type'       => 'nullable|in:all,pemasukan,pengeluaran',
            'tahun_ajaran' => 'nullable|string|max:15',
        ]);

        $waliKelas = $this->getWaliKelas();
        $kelasList = $waliKelas->kelas()->with(['bendahara.user'])->get();

        if ($kelasList->isEmpty()) {
            return view('wali-kelas.reports.index', [
                'kelasList' => collect(),
                'kelas' => null,
                'activeTab' => 'buku_kas',
                'transactions' => collect(),
                'saldoAwal' => 0,
                'totalIncome' => 0,
                'totalExpense' => 0,
                'netFlow' => 0,
                'saldoAkhir' => 0,
                'months' => [],
                'incomeData' => [],
                'expenseData' => [],
                'startDate' => now()->startOfYear()->format('Y-m-d'),
                'endDate' => now()->format('Y-m-d'),
                'categoryId' => null,
                'type' => 'all',
                'categories' => collect(),
                'selectedKodeKelas' => null,
                'tahunAjaranList' => [],
                'selectedTahunAjaran' => '2025/2026',
                'periods' => [],
                'feeAmount' => 0,
                'studentsArrears' => collect(),
                'totalSiswa' => 0,
                'totalTargetKas' => 0,
                'totalKasTerkumpul' => 0,
                'totalSisaTunggakan' => 0,
                'persentaseLunas' => 0,
                'countLunas' => 0,
                'countSebagian' => 0,
                'countBelum' => 0,
            ]);
        }

        $selectedKodeKelas = $request->input('kode_kelas', $kelasList->first()->kode_kelas);
        $kelas = $kelasList->where('kode_kelas', $selectedKodeKelas)->first() ?? $kelasList->first();
        $kodeKelas = $kelas->kode_kelas;
        $activeTab = $request->input('tab', 'buku_kas');

        // Date Filtering (Default: awal tahun s.d. hari ini)
        $startDate = $request->input('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $categoryId = $request->input('category_id');
        $type = $request->input('type', 'all');

        // Base query for this class
        $baseQuery = TransaksiKas::where('kode_kelas', $kodeKelas);

        // Calculate Saldo Awal (sebelum tanggal mulai)
        $incomeBefore = (clone $baseQuery)
            ->whereDate('tanggal_transaksi', '<', $startDate)
            ->where('jenis_transaksi', 'pemasukan')
            ->sum('total_nominal');

        $expenseBefore = (clone $baseQuery)
            ->whereDate('tanggal_transaksi', '<', $startDate)
            ->where('jenis_transaksi', 'pengeluaran')
            ->sum('total_nominal');

        $saldoAwal = (float)($incomeBefore - $expenseBefore);

        // Filtered transactions query within date range
        $query = (clone $baseQuery)
            ->with(['kategori', 'bendahara.user', 'detailTransaksi.siswa'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate]);

        if ($categoryId) {
            $query->where('id_kategori', $categoryId);
        }

        if ($type && $type !== 'all') {
            $query->where('jenis_transaksi', $type);
        }

        // Summary metrics in the filtered range
        $totalIncome = (float)(clone $query)->where('jenis_transaksi', 'pemasukan')->sum('total_nominal');
        $totalExpense = (float)(clone $query)->where('jenis_transaksi', 'pengeluaran')->sum('total_nominal');
        $netFlow = $totalIncome - $totalExpense;
        $saldoAkhir = $saldoAwal + $netFlow;

        // Transactions list sorted ascending to calculate running balance
        $rawTransactions = (clone $query)->orderBy('tanggal_transaksi', 'asc')->orderBy('id', 'asc')->get();

        $running = $saldoAwal;
        $transactionsWithBalance = $rawTransactions->map(function ($tx) use (&$running) {
            if ($tx->jenis_transaksi === 'pemasukan') {
                $running += (float)$tx->total_nominal;
            } else {
                $running -= (float)$tx->total_nominal;
            }
            $tx->running_balance = $running;
            return $tx;
        });

        // Display latest first in table view
        $transactions = $transactionsWithBalance->reverse()->values();

        // Chart Data (Group by Month for the selected range)
        $driver = DB::connection()->getDriverName();
        $dateSelect = $driver === 'sqlite'
            ? 'strftime("%Y-%m", tanggal_transaksi) as month'
            : 'DATE_FORMAT(tanggal_transaksi, "%Y-%m") as month';

        $chartQuery = TransaksiKas::where('kode_kelas', $kodeKelas)
            ->selectRaw($dateSelect . ', jenis_transaksi, SUM(total_nominal) as total')
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->groupBy('month', 'jenis_transaksi')
            ->orderBy('month')
            ->get();

        $months = [];
        $incomeData = [];
        $expenseData = [];

        try {
            $period = CarbonPeriod::create($startDate, '1 month', $endDate);
            foreach ($period as $date) {
                $monthKey = $date->format('Y-m');
                $months[] = $date->translatedFormat('M Y');

                $inc = $chartQuery->where('month', $monthKey)->where('jenis_transaksi', 'pemasukan')->first();
                $incomeData[] = $inc ? (float)$inc->total : 0;

                $exp = $chartQuery->where('month', $monthKey)->where('jenis_transaksi', 'pengeluaran')->first();
                $expenseData[] = $exp ? (float)$exp->total : 0;
            }
        } catch (\Exception $e) {
            $months = [];
            $incomeData = [];
            $expenseData = [];
        }

        $categories = Kategori::orderBy('nama_kategori')->get();

        // ==========================================
        // TAB 2: STATUS TUNGGAKAN SISWA (PIUTANG KAS)
        // ==========================================
        $tahunAjaranList = Kelas::getTahunAjaranOptions();
        $selectedTahunAjaran = $request->input('tahun_ajaran', $kelas->tahun_ajaran ?? '2025/2026');
        $periods = $kelas ? $kelas->getPeriods($selectedTahunAjaran) : [];
        $feeAmount = (float)($kelas->nominal_standar ?? 20000);
        $totalPeriodsCount = count($periods);
        $targetPerStudent = $totalPeriodsCount * $feeAmount;

        $studentsArrears = Siswa::where('kode_kelas', $kodeKelas)
            ->with(['detailTransaksiKas' => function ($q) use ($selectedTahunAjaran) {
                $q->where(function ($sq) use ($selectedTahunAjaran) {
                    $sq->where('tahun_ajaran', $selectedTahunAjaran)
                       ->orWhereNull('tahun_ajaran');
                });
            }, 'user'])
            ->orderBy('nama', 'asc')
            ->get()
            ->map(function ($student) use ($periods, $feeAmount, $totalPeriodsCount, $targetPerStudent) {
                $periodTotals = $student->detailTransaksiKas
                    ->whereNotNull('periode')
                    ->groupBy('periode')
                    ->map(fn($group) => (float)$group->sum('nominal'));

                $paidSlots = 0;
                $paidPeriodKeys = [];
                foreach ($periods as $pItem) {
                    $key = $pItem['key'];
                    $paid = $periodTotals->get($key, 0.0);
                    if ($paid <= 0 && $periodTotals->has($pItem['month'])) {
                        $paid = $periodTotals->get($pItem['month'], 0.0);
                    }
                    if ($paid >= $feeAmount) {
                        $paidSlots++;
                        $paidPeriodKeys[] = $key;
                    }
                }

                $student->total_dibayar = (float)$student->detailTransaksiKas->sum('nominal');
                $student->target_nominal = $targetPerStudent;
                $student->total_tunggakan = max(0.0, $targetPerStudent - $student->total_dibayar);
                $student->slots_paid = $paidSlots;
                $student->slots_unpaid = max(0, $totalPeriodsCount - $paidSlots);
                $student->paid_periods = $paidPeriodKeys;

                if ($student->total_tunggakan <= 0 && $targetPerStudent > 0) {
                    $student->status_bayar = 'Lunas';
                } elseif ($student->total_dibayar > 0) {
                    $student->status_bayar = 'Sebagian';
                } else {
                    $student->status_bayar = 'Belum Bayar';
                }

                return $student;
            });

        $totalSiswa = $studentsArrears->count();
        $totalTargetKas = $totalSiswa * $targetPerStudent;
        $totalKasTerkumpul = $studentsArrears->sum('total_dibayar');
        $totalSisaTunggakan = $studentsArrears->sum('total_tunggakan');
        $persentaseLunas = $totalTargetKas > 0 ? round(($totalKasTerkumpul / $totalTargetKas) * 100, 1) : 0;
        $countLunas = $studentsArrears->where('status_bayar', 'Lunas')->count();
        $countSebagian = $studentsArrears->where('status_bayar', 'Sebagian')->count();
        $countBelum = $studentsArrears->where('status_bayar', 'Belum Bayar')->count();

        return view('wali-kelas.reports.index', compact(
            'kelasList',
            'kelas',
            'activeTab',
            'selectedKodeKelas',
            'transactions',
            'saldoAwal',
            'totalIncome',
            'totalExpense',
            'netFlow',
            'saldoAkhir',
            'months',
            'incomeData',
            'expenseData',
            'startDate',
            'endDate',
            'categoryId',
            'type',
            'categories',
            // Tab 2 data
            'tahunAjaranList',
            'selectedTahunAjaran',
            'periods',
            'feeAmount',
            'studentsArrears',
            'totalSiswa',
            'totalTargetKas',
            'totalKasTerkumpul',
            'totalSisaTunggakan',
            'persentaseLunas',
            'countLunas',
            'countSebagian',
            'countBelum'
        ));
    }

    /**
     * Export report to PDF for Wali Kelas.
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'kode_kelas' => 'nullable|string',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date'   => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'category_id'=> 'nullable|exists:kategori,id',
            'type'       => 'nullable|in:all,pemasukan,pengeluaran',
        ]);

        $waliKelas = $this->getWaliKelas();
        $kelasList = $waliKelas->kelas()->get();

        if ($kelasList->isEmpty()) {
            abort(404, 'Tidak ada kelas yang ditemukan.');
        }

        $selectedKodeKelas = $request->input('kode_kelas', $kelasList->first()->kode_kelas);
        $kelas = $kelasList->where('kode_kelas', $selectedKodeKelas)->first() ?? $kelasList->first();
        $kodeKelas = $kelas->kode_kelas;

        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $categoryId = $request->input('category_id');
        if (empty($categoryId)) {
            $categoryId = null;
        }
        $type = $request->input('type', 'all');

        $baseQuery = TransaksiKas::where('kode_kelas', $kodeKelas);

        if ($categoryId) {
            $baseQuery->where('id_kategori', $categoryId);
        }

        if ($type && $type !== 'all') {
            $baseQuery->where('jenis_transaksi', $type);
        }

        $incomeBefore = (clone $baseQuery)
            ->whereDate('tanggal_transaksi', '<', $startDate)
            ->where('jenis_transaksi', 'pemasukan')
            ->sum('total_nominal');

        $expenseBefore = (clone $baseQuery)
            ->whereDate('tanggal_transaksi', '<', $startDate)
            ->where('jenis_transaksi', 'pengeluaran')
            ->sum('total_nominal');

        $saldoAwal = (float)($incomeBefore - $expenseBefore);

        $query = (clone $baseQuery)
            ->with(['kategori', 'bendahara.user', 'detailTransaksi.siswa'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate]);

        $totalIncome = (float)(clone $query)->where('jenis_transaksi', 'pemasukan')->sum('total_nominal');
        $totalExpense = (float)(clone $query)->where('jenis_transaksi', 'pengeluaran')->sum('total_nominal');
        $saldoAkhir = $saldoAwal + ($totalIncome - $totalExpense);

        $transactions = $query->orderBy('tanggal_transaksi', 'asc')->orderBy('id', 'asc')->get();

        $running = $saldoAwal;
        foreach ($transactions as $tx) {
            if ($tx->jenis_transaksi === 'pemasukan') {
                $running += (float)$tx->total_nominal;
            } else {
                $running -= (float)$tx->total_nominal;
            }
            $tx->running_balance = $running;
        }

        $bendahara = $kelas->bendahara()->first();

        $pdf = Pdf::loadView('exports.report-pdf', compact(
            'kelas',
            'bendahara',
            'waliKelas',
            'transactions',
            'saldoAwal',
            'totalIncome',
            'totalExpense',
            'saldoAkhir',
            'startDate',
            'endDate'
        ))->setPaper('a4', 'portrait');

        $filename = 'laporan-kas-walikelas-' . $kodeKelas . '-' . now()->format('YmdHis') . '.pdf';

        if ($request->has('download') && $request->input('download') == '1') {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    /**
     * Export report to Excel (.xlsx) for Wali Kelas.
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'kode_kelas' => 'nullable|string',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date'   => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'category_id'=> 'nullable',
            'type'       => 'nullable|in:all,pemasukan,pengeluaran',
        ]);

        $waliKelas = $this->getWaliKelas();
        $kelasList = $waliKelas->kelas()->get();

        if ($kelasList->isEmpty()) {
            abort(404, 'Tidak ada kelas yang ditemukan.');
        }

        $selectedKodeKelas = $request->input('kode_kelas', $kelasList->first()->kode_kelas);
        $kelas = $kelasList->where('kode_kelas', $selectedKodeKelas)->first() ?? $kelasList->first();
        $kodeKelas = $kelas->kode_kelas;

        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $categoryId = $request->input('category_id');
        if (empty($categoryId)) {
            $categoryId = null;
        }
        $type = $request->input('type', 'all');

        $filename = 'laporan-kas-walikelas-' . $kodeKelas . '-' . now()->format('YmdHis') . '.xlsx';

        return Excel::download(
            new BendaharaReportExport($kodeKelas, $startDate, $endDate, $categoryId, $type),
            $filename
        );
    }

    /**
     * Export student arrears matrix to Excel for Wali Kelas.
     */
    public function exportTunggakanExcel(Request $request)
    {
        $waliKelas = $this->getWaliKelas();
        $kelasList = $waliKelas->kelas()->get();

        if ($kelasList->isEmpty()) {
            abort(404, 'Tidak ada kelas yang ditemukan.');
        }

        $selectedKodeKelas = $request->input('kode_kelas', $kelasList->first()->kode_kelas);
        $kelas = $kelasList->where('kode_kelas', $selectedKodeKelas)->first() ?? $kelasList->first();
        $selectedTahunAjaran = $request->input('tahun_ajaran', $kelas->tahun_ajaran ?? '2025/2026');

        $filename = 'laporan-tunggakan-walikelas-' . $kelas->kode_kelas . '-' . str_replace('/', '-', $selectedTahunAjaran) . '.xlsx';

        return Excel::download(
            new BendaharaStudentExport($kelas->kode_kelas, $selectedTahunAjaran),
            $filename
        );
    }

    /**
     * Export student arrears matrix to PDF for Wali Kelas.
     */
    public function exportTunggakanPdf(Request $request)
    {
        $waliKelas = $this->getWaliKelas();
        $kelasList = $waliKelas->kelas()->get();

        if ($kelasList->isEmpty()) {
            abort(404, 'Tidak ada kelas yang ditemukan.');
        }

        $selectedKodeKelas = $request->input('kode_kelas', $kelasList->first()->kode_kelas);
        $kelas = $kelasList->where('kode_kelas', $selectedKodeKelas)->first() ?? $kelasList->first();
        $selectedTahunAjaran = $request->input('tahun_ajaran', $kelas->tahun_ajaran ?? '2025/2026');
        $feeAmount = (float)($kelas->nominal_standar ?? 20000);
        $periods = $kelas->getPeriods($selectedTahunAjaran);
        $totalPeriods = count($periods);

        $students = Siswa::where('kode_kelas', $kelas->kode_kelas)
            ->with(['detailTransaksiKas' => function ($q) use ($selectedTahunAjaran) {
                $q->where(function ($sq) use ($selectedTahunAjaran) {
                    $sq->where('tahun_ajaran', $selectedTahunAjaran)
                       ->orWhereNull('tahun_ajaran');
                });
            }, 'user'])
            ->orderBy('nama', 'asc')
            ->get();

        $headings = ['No', 'Nama Siswa', 'NIS', 'No. HP', 'Dibayar (Rp)', 'Tunggakan (Rp)', 'Status'];
        $rows = [];
        $no = 1;

        foreach ($students as $student) {
            $totalDibayar = (float)$student->detailTransaksiKas->sum('nominal');
            $totalTunggakan = max(0.0, $totalPeriods * $feeAmount - $totalDibayar);

            $status = 'Up to Date';
            if ($totalTunggakan > 0) {
                $status = ($totalDibayar > 0) ? 'Pending' : 'Overdue';
            }

            $rows[] = [
                $no++,
                $student->nama,
                $student->nis ?? '-',
                $student->no_hp ?? '-',
                number_format($totalDibayar, 0, ',', '.'),
                number_format($totalTunggakan, 0, ',', '.'),
                $status,
            ];
        }

        $pdf = Pdf::loadView('exports.pdf', [
            'title' => 'Rekap Tunggakan Kas Siswa ' . ($kelas->nama_kelas ?? $kelas->kode_kelas) . ' TA ' . $selectedTahunAjaran,
            'kelas' => $kelas,
            'headings' => $headings,
            'rows' => $rows,
        ]);

        return $pdf->download('laporan-tunggakan-walikelas-' . $kelas->kode_kelas . '-' . str_replace('/', '-', $selectedTahunAjaran) . '.pdf');
    }
}
