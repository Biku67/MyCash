<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Kategori;
use App\Models\TransaksiKas;
use App\Models\DetailTransaksiKas;
use App\Models\LogTransaksiKas;
use App\Exports\BendaharaStudentExport;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Services\KasNotificationService;

class StudentController extends Controller
{
    /**
     * Get the authenticated bendahara record.
     */
    private function getBendahara()
    {
        $bendahara = auth()->user()->bendahara;
        if (!$bendahara) {
            abort(403, 'Akses ditolak. Anda belum terdaftar sebagai bendahara kelas.');
        }
        return $bendahara;
    }

    /**
     * Display student management and kas attendance matrix.
     */
    public function index(Request $request)
    {
        $bendahara = $this->getBendahara();
        $kelas = $bendahara->kelas;

        $feePeriodType = $kelas->tipe_periode ?? 'bulanan';
        $feeAmount = (float)($kelas->nominal_standar ?? 20000);
        $monthList = Kelas::getMonthList();
        $tahunAjaranList = Kelas::getTahunAjaranOptions();
        $selectedTahunAjaran = $request->query('tahun_ajaran', $kelas->tahun_ajaran ?? '2025/2026');

        $periods = $kelas->getPeriods($selectedTahunAjaran);

        $students = Siswa::where('kode_kelas', $bendahara->kode_kelas)
            ->with(['detailTransaksiKas' => function ($query) use ($selectedTahunAjaran) {
                $query->where(function ($q) use ($selectedTahunAjaran) {
                    $q->where('tahun_ajaran', $selectedTahunAjaran)
                      ->orWhereNull('tahun_ajaran');
                });
            }, 'user'])
            ->orderBy('nama', 'asc')
            ->get()
            ->map(function ($student) use ($periods, $feeAmount) {
                // Group details by period to check how much is paid for each period
                $periodTotals = $student->detailTransaksiKas
                    ->whereNotNull('periode')
                    ->groupBy('periode')
                    ->map(fn($group) => (float)$group->sum('nominal'));

                $paidPeriods = [];
                $partialPeriods = [];
                foreach ($periods as $pItem) {
                    $periodKey = $pItem['key'];
                    $periodPaid = $periodTotals->get($periodKey, 0.0);
                    if ($periodPaid <= 0 && $periodTotals->has($pItem['month'])) {
                        $periodPaid = $periodTotals->get($pItem['month'], 0.0);
                    }

                    if ($periodPaid >= $feeAmount) {
                        $paidPeriods[] = $periodKey;
                    } elseif ($periodPaid > 0) {
                        $partialPeriods[$periodKey] = $periodPaid;
                    }
                }

                $student->paid_periods = $paidPeriods;
                $student->partial_periods = $partialPeriods;

                // Total actual contributed is the SUM of nominals in this academic year
                $student->contributed = (float)$student->detailTransaksiKas->sum('nominal');

                $totalPeriods = count($periods);
                $totalTarget = $totalPeriods * $feeAmount;
                $student->outstanding_debt = max(0.0, $totalTarget - $student->contributed);

                if ($student->outstanding_debt <= 0) {
                    $student->status = 'Up to Date';
                } elseif ($student->contributed > 0) {
                    $student->status = 'Pending';
                } else {
                    $student->status = 'Overdue';
                }

                return $student;
            });

        return view('bendahara.students.index', compact(
            'students', 'periods', 'feePeriodType', 'feeAmount', 'kelas', 'monthList', 'tahunAjaranList', 'selectedTahunAjaran'
        ));
    }

    /**
     * Update fee parameter settings for the class.
     */
    public function updateFeeSettings(Request $request)
    {
        $validated = $request->validate([
            'fee_period_type' => 'required|in:weekly,monthly,mingguan,bulanan',
            'fee_amount' => 'required|numeric|min:0',
            'start_month' => 'nullable|string|in:Jan,Feb,Mar,Apr,Mei,Jun,Jul,Agt,Sep,Okt,Nov,Des',
            'end_month' => 'nullable|string|in:Jan,Feb,Mar,Apr,Mei,Jun,Jul,Agt,Sep,Okt,Nov,Des',
            'tahun_ajaran' => 'nullable|string|max:15',
        ]);

        $bendahara = $this->getBendahara();
        $tipePeriode = in_array($validated['fee_period_type'], ['weekly', 'mingguan']) ? 'mingguan' : 'bulanan';

        $dataToUpdate = [
            'tipe_periode' => $tipePeriode,
            'nominal_standar' => $validated['fee_amount'],
        ];

        if (!empty($validated['tahun_ajaran'])) {
            $dataToUpdate['tahun_ajaran'] = $validated['tahun_ajaran'];
        }

        if ($tipePeriode === 'bulanan') {
            if (!empty($validated['start_month'])) {
                $dataToUpdate['bulan_mulai'] = $validated['start_month'];
            }
            if (!empty($validated['end_month'])) {
                $dataToUpdate['bulan_selesai'] = $validated['end_month'];
            }
        }

        $bendahara->kelas->update($dataToUpdate);

        if ($request->has('tahun_ajaran')) {
            return redirect()->route('bendahara.students.index', ['tahun_ajaran' => $dataToUpdate['tahun_ajaran'] ?? $bendahara->kelas->tahun_ajaran])
                ->with('success', 'Pengaturan parameter iuran kas kelas berhasil diperbarui.');
        }

        return redirect()->route('bendahara.students.index')
            ->with('success', 'Pengaturan parameter iuran kas kelas berhasil diperbarui.');
    }

    /**
     * Tutup Buku & Rollover Saldo Kas to Next Academic Year.
     */
    public function tutupBuku(Request $request)
    {
        $bendahara = $this->getBendahara();
        $kelas = $bendahara->kelas;
        $currentTa = $kelas->tahun_ajaran ?? '2025/2026';

        // Calculate current physical cash balance for the class (all time)
        $totalPemasukan = (float)TransaksiKas::where('kode_kelas', $bendahara->kode_kelas)
            ->where('jenis_transaksi', 'pemasukan')
            ->sum('total_nominal');
        $totalPengeluaran = (float)TransaksiKas::where('kode_kelas', $bendahara->kode_kelas)
            ->where('jenis_transaksi', 'pengeluaran')
            ->sum('total_nominal');
        $sisaSaldo = max(0.0, $totalPemasukan - $totalPengeluaran);

        // Determine next academic year
        $parts = explode('/', $currentTa);
        $startYear = (int)($parts[0] ?? date('Y'));
        $endYear = (int)($parts[1] ?? ($startYear + 1));
        $nextTa = ($startYear + 1) . '/' . ($endYear + 1);

        \Illuminate\Support\Facades\DB::transaction(function () use ($bendahara, $kelas, $currentTa, $nextTa, $sisaSaldo) {
            // Find or create Category 'Saldo Awal'
            $kategori = Kategori::firstOrCreate([
                'nama_kategori' => 'Saldo Awal',
                'tipe' => 'pemasukan',
            ]);

            // If there is physical cash to carry over, create the initial balance transaction
            if ($sisaSaldo > 0) {
                $transaksi = TransaksiKas::create([
                    'kode_kelas' => $bendahara->kode_kelas,
                    'id_bendahara' => $bendahara->id,
                    'id_kategori' => $kategori->id,
                    'jenis_transaksi' => 'pemasukan',
                    'tahun_ajaran' => $nextTa,
                    'total_nominal' => $sisaSaldo,
                    'tanggal_transaksi' => now()->format('Y-m-d'),
                    'keterangan' => 'Saldo Awal Pindahan TA ' . $currentTa,
                ]);

                \App\Models\LogTransaksiKas::create([
                    'id_transaksi_kas' => $transaksi->id,
                    'kode_kelas' => $bendahara->kode_kelas,
                    'user_id' => auth()->id(),
                    'aksi' => 'create',
                    'alasan' => 'Tutup Buku & Rollover Saldo Awal TA ' . $nextTa,
                    'data_sesudahnya' => [
                        'keterangan' => $transaksi->keterangan,
                        'total_nominal' => $sisaSaldo,
                        'tahun_ajaran' => $nextTa,
                    ],
                ]);
            }

            // Update active Tahun Ajaran on Kelas
            $kelas->update([
                'tahun_ajaran' => $nextTa,
            ]);
        });

        return redirect()->route('bendahara.students.index', ['tahun_ajaran' => $nextTa])
            ->with('success', "Tutup Buku berhasil! Tahun Ajaran {$nextTa} telah aktif. Sisa saldo Rp " . number_format($sisaSaldo, 0, ',', '.') . " berhasil dipindahkan sebagai Saldo Awal.");
    }

    /**
     * Reset kas checklist matrix so it becomes empty for a new period.
     */
    public function resetMatrix(Request $request)
    {
        $bendahara = $this->getBendahara();
        $bendahara->kelas->update(['last_reset_at' => now()]);

        return redirect()->route('bendahara.students.index')
            ->with('success', 'Matriks checklist kas berhasil di-reset menjadi kosong untuk periode baru. Riwayat keuangan tetap tersimpan di Laporan Kas.');
    }

    /**
     * AJAX Toggle kas attendance checkbox per period per student.
     */
    public function toggleAbsensi(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:siswa,id',
            'period' => 'required|string',
            'status' => 'required|boolean',
            'tahun_ajaran' => 'nullable|string|max:15',
        ]);

        $bendahara = $this->getBendahara();
        $student = Siswa::where('id', $request->student_id)
            ->where('kode_kelas', $bendahara->kode_kelas)
            ->firstOrFail();

        $kelas = $bendahara->kelas;
        $ta = $request->input('tahun_ajaran', $kelas->tahun_ajaran ?? '2025/2026');
        $feeAmount = (float)($kelas->nominal_standar ?? 20000);

        DB::transaction(function () use ($request, $bendahara, $student, $feeAmount, $ta) {
            if ($request->status) {
                // Check what is already paid for this period in this academic year
                $currentPaidForPeriod = (float)DetailTransaksiKas::where('id_siswa', $student->id)
                    ->where('periode', $request->period)
                    ->where(function ($q) use ($ta) {
                        $q->where('tahun_ajaran', $ta)
                          ->orWhereNull('tahun_ajaran');
                    })
                    ->sum('nominal');

                $amountToPay = max(0.0, $feeAmount - $currentPaidForPeriod);

                if ($amountToPay > 0) {
                    $kategori = Kategori::firstOrCreate(
                        ['nama_kategori' => 'Uang Kas', 'tipe' => 'pemasukan']
                    );

                    $transaksi = TransaksiKas::create([
                        'kode_kelas' => $bendahara->kode_kelas,
                        'id_bendahara' => $bendahara->id,
                        'id_kategori' => $kategori->id,
                        'jenis_transaksi' => 'pemasukan',
                        'tahun_ajaran' => $ta,
                        'total_nominal' => $amountToPay,
                        'tanggal_transaksi' => now()->format('Y-m-d'),
                        'keterangan' => "Pembayaran kas {$request->period} - {$student->nama}",
                    ]);

                    DetailTransaksiKas::create([
                        'id_transaksi_kas' => $transaksi->id,
                        'id_siswa' => $student->id,
                        'periode' => $request->period,
                        'tahun_ajaran' => $ta,
                        'nominal' => $amountToPay,
                    ]);

                    LogTransaksiKas::create([
                        'id_transaksi_kas' => $transaksi->id,
                        'kode_kelas' => $bendahara->kode_kelas,
                        'user_id' => auth()->id(),
                        'aksi' => 'create',
                        'alasan' => "Checklist kas otomatis periode {$request->period} ({$student->nama})",
                        'data_sesudahnya' => [
                            'keterangan' => $transaksi->keterangan,
                            'jenis_transaksi' => 'pemasukan',
                            'total_nominal' => $amountToPay,
                            'student_name' => $student->nama,
                            'periode' => $request->period,
                            'tahun_ajaran' => $ta,
                        ],
                    ]);

                    KasNotificationService::notifyStudentPayment(
                        $transaksi,
                        $student,
                        [$request->period],
                        [],
                        $amountToPay,
                        $bendahara
                    );
                }
            } else {
                // Remove checklist
                $details = DetailTransaksiKas::where('id_siswa', $student->id)
                    ->where('periode', $request->period)
                    ->where(function ($q) use ($ta) {
                        $q->where('tahun_ajaran', $ta)
                          ->orWhereNull('tahun_ajaran');
                    })
                    ->get();

                foreach ($details as $detail) {
                    $tx = $detail->transaksiKas;
                    if ($tx) {
                        LogTransaksiKas::create([
                            'id_transaksi_kas' => $tx->id,
                            'kode_kelas' => $bendahara->kode_kelas,
                            'user_id' => auth()->id(),
                            'aksi' => 'delete',
                            'alasan' => "Batal checklist kas periode {$request->period} ({$student->nama})",
                            'data_sebelumnya' => [
                                'keterangan' => $tx->keterangan,
                                'total_nominal' => (float)$tx->total_nominal,
                                'student_name' => $student->nama,
                                'periode' => $request->period,
                                'tahun_ajaran' => $ta,
                            ],
                        ]);
                    }

                    $detail->delete();

                    // If transaction has no more details and was specifically for this period, delete it
                    if ($tx && $tx->detailTransaksi()->count() === 0 && str_starts_with($tx->keterangan, 'Pembayaran kas')) {
                        $tx->delete();
                    }
                }
            }
        });

        // Recalculate totals for this academic year
        $totalPaid = (float)DetailTransaksiKas::where('id_siswa', $student->id)
            ->where(function ($q) use ($ta) {
                $q->where('tahun_ajaran', $ta)
                  ->orWhereNull('tahun_ajaran');
            })
            ->sum('nominal');
        $totalPeriods = count($kelas->getPeriods($ta));
        $totalDebt = max(0.0, ($totalPeriods * $feeAmount) - $totalPaid);

        return response()->json([
            'success' => true,
            'total_paid' => $totalPaid,
            'total_debt' => $totalDebt,
            'total_paid_formatted' => 'Rp ' . number_format($totalPaid, 0, ',', '.'),
            'total_debt_formatted' => 'Rp ' . number_format($totalDebt, 0, ',', '.'),
        ]);
    }

    /**
     * Export student matrix to Excel.
     */
    public function exportExcel(Request $request)
    {
        $bendahara = $this->getBendahara();
        $selectedTahunAjaran = $request->query('tahun_ajaran', $bendahara->kelas->tahun_ajaran ?? '2025/2026');
        return Excel::download(
            new BendaharaStudentExport($bendahara->kode_kelas, $selectedTahunAjaran),
            'data-siswa-' . $bendahara->kode_kelas . '-' . str_replace('/', '-', $selectedTahunAjaran) . '.xlsx'
        );
    }

    /**
     * Export student matrix to PDF.
     */
    public function exportPdf(Request $request)
    {
        $bendahara = $this->getBendahara();
        $kelas = $bendahara->kelas;
        $selectedTahunAjaran = $request->query('tahun_ajaran', $kelas->tahun_ajaran ?? '2025/2026');
        $feeAmount = (float)($kelas->nominal_standar ?? 20000);
        $periods = $kelas->getPeriods($selectedTahunAjaran);
        $totalPeriods = count($periods);

        $students = Siswa::where('kode_kelas', $bendahara->kode_kelas)
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
            'title' => 'Daftar Siswa & Status Kas TA ' . $selectedTahunAjaran,
            'kelas' => $kelas,
            'headings' => $headings,
            'rows' => $rows,
        ]);

        return $pdf->download('siswa-kas-' . $bendahara->kode_kelas . '-' . str_replace('/', '-', $selectedTahunAjaran) . '.pdf');
    }
}
