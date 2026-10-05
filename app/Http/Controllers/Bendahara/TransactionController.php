<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransaksiKas;
use App\Models\Kategori;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\DetailTransaksiKas;
use App\Models\LogTransaksiKas;
use App\Exports\BendaharaTransactionExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Services\KasNotificationService;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $bendahara = auth()->user()->bendahara;

        if ($request->ajax()) {
            $transactions = TransaksiKas::with(['kategori', 'bendahara', 'detailTransaksi.siswa'])
                ->where('kode_kelas', $bendahara->kode_kelas);

            return DataTables::of($transactions)
                ->addIndexColumn()
                ->orderColumn('tanggal_transaksi', function ($query, $order) {
                    $query->orderBy('tanggal_transaksi', $order)->orderBy('id', 'desc');
                })
                ->orderColumn('total_nominal', function ($query, $order) {
                    $query->orderBy('total_nominal', $order);
                })
                ->orderColumn('jenis_transaksi', function ($query, $order) {
                    $query->orderBy('jenis_transaksi', $order);
                })
                ->editColumn('tanggal_transaksi', function ($tx) {
                    return Carbon::parse($tx->tanggal_transaksi)->format('d M Y');
                })
                ->addColumn('category_badge', function ($tx) {
                    $categoryName = $tx->kategori->nama_kategori ?? '-';
                    if ($categoryName === 'Uang Kas') {
                        return '<span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-teal-50 text-teal-700 border border-teal-200/60 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>' . e($categoryName) . '</span>';
                    } elseif ($tx->jenis_transaksi === 'pemasukan') {
                        return '<span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>' . e($categoryName) . '</span>';
                    } else {
                        return '<span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-slate-100 text-slate-700 border border-slate-200/60 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>' . e($categoryName) . '</span>';
                    }
                })
                ->addColumn('keterangan_display', function ($tx) {
                    $desc = e($tx->keterangan ?? '-');
                    $studentName = $tx->detailTransaksi->first()?->siswa?->nama;
                    if ($studentName) {
                        return '<div><span class="text-gray-800 font-medium">' . $desc . '</span><br><span class="text-[11px] text-gray-400 inline-flex items-center gap-1"><span class="material-symbols-outlined text-xs">person</span>' . e($studentName) . '</span></div>';
                    }
                    return '<span class="text-gray-800 font-medium">' . $desc . '</span>';
                })
                ->addColumn('type_badge', function ($tx) {
                    if ($tx->jenis_transaksi === 'pemasukan') {
                        return '<span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1"><span class="material-symbols-outlined text-xs">arrow_downward</span>Pemasukan</span>';
                    }
                    return '<span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-red-50 text-red-600 border border-red-200/60 inline-flex items-center gap-1"><span class="material-symbols-outlined text-xs">arrow_upward</span>Pengeluaran</span>';
                })
                ->addColumn('amount_display', function ($tx) {
                    $class = $tx->jenis_transaksi === 'pemasukan' ? 'text-status-lunas' : 'text-status-menunggak';
                    return '<span class="font-semibold ' . $class . '">Rp ' . number_format($tx->total_nominal, 0, ',', '.') . '</span>';
                })
                ->addColumn('actions', function ($tx) {
                    $editUrl = route('bendahara.transactions.edit', $tx->id);
                    $deleteUrl = route('bendahara.transactions.destroy', $tx->id);
                    $desc = htmlspecialchars($tx->keterangan, ENT_QUOTES, 'UTF-8');
                    $amount = number_format($tx->total_nominal, 0, ',', '.');

                    return '
                    <div class="flex items-center justify-center gap-2">
                        <a href="' . $editUrl . '" class="p-1.5 text-gray-500 hover:text-navy hover:bg-gray-100 rounded-lg transition-colors" title="Edit Transaksi">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </a>
                        <button type="button" onclick="confirmDeleteTransaction(\'' . $deleteUrl . '\', \'' . $desc . '\', \'' . $amount . '\')" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Transaksi">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>';
                })
                ->rawColumns(['category_badge', 'keterangan_display', 'type_badge', 'amount_display', 'actions'])
                ->make(true);
        }

        $logsCount = LogTransaksiKas::where('kode_kelas', $bendahara->kode_kelas)
            ->where('is_read', false)
            ->count();

        return view('bendahara.transactions.index', compact('logsCount'));
    }

    /**
     * Export transactions to Excel.
     */
    public function exportExcel()
    {
        $bendahara = auth()->user()->bendahara;
        return Excel::download(
            new BendaharaTransactionExport($bendahara->kode_kelas),
            'transaksi-kas-' . $bendahara->kode_kelas . '-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export transactions to PDF.
     */
    public function exportPdf()
    {
        $bendahara = auth()->user()->bendahara;
        $kelas = $bendahara->kelas;

        $transactions = TransaksiKas::where('kode_kelas', $bendahara->kode_kelas)
            ->with(['kategori', 'bendahara'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $headings = ['No', 'Tanggal', 'Jenis', 'Kategori', 'Keterangan', 'Nominal (Rp)', 'Dicatat Oleh'];
        $rows = [];
        $no = 1;

        foreach ($transactions as $tx) {
            $rows[] = [
                $no++,
                Carbon::parse($tx->tanggal_transaksi)->format('d/m/Y'),
                ucfirst($tx->jenis_transaksi),
                $tx->kategori->nama_kategori ?? '-',
                $tx->keterangan,
                ($tx->jenis_transaksi === 'pemasukan' ? '+' : '-') . ' Rp ' . number_format($tx->total_nominal, 0, ',', '.'),
                $tx->bendahara->nama ?? 'Bendahara',
            ];
        }

        $pdf = Pdf::loadView('exports.pdf', [
            'title' => 'Laporan Transaksi Kas',
            'kelas' => $kelas,
            'headings' => $headings,
            'rows' => $rows,
        ]);

        return $pdf->download('transaksi-kas-' . $bendahara->kode_kelas . '-' . now()->format('Y-m-d') . '.pdf');
    }

    public function create()
    {
        $bendahara = auth()->user()->bendahara;
        $students = Siswa::where('kode_kelas', $bendahara->kode_kelas)->orderBy('nama', 'asc')->get();
        
        $incomeCategories = Kategori::where('tipe', 'pemasukan')->pluck('nama_kategori')->values();
        if ($incomeCategories->isEmpty()) {
            $incomeCategories = collect(['Uang Kas', 'Sumbangan / Donasi', 'Lainnya (Pemasukan)']);
        }
        
        $expenseCategories = Kategori::where('tipe', 'pengeluaran')->pluck('nama_kategori')->values();
        if ($expenseCategories->isEmpty()) {
            $expenseCategories = collect(['Pembelian ATK', 'Kegiatan Kelas', 'Operasional Kelas', 'Lainnya (Pengeluaran)']);
        }

        return view('bendahara.transactions.create', compact('students', 'incomeCategories', 'expenseCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'transaction_date' => 'required|date|before_or_equal:today',
            'category' => 'required|string',
            'student_id' => 'required_if:category,Uang Kas|nullable|exists:siswa,id',
        ], [
            'transaction_date.before_or_equal' => 'Tanggal transaksi tidak boleh melebihi hari ini.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $bendahara = auth()->user()->bendahara;
            $kelas = $bendahara->kelas;
            $studentId = $validated['student_id'] ?? null;
            $description = $validated['description'];
            $jenisTransaksi = $validated['type'] === 'income' ? 'pemasukan' : 'pengeluaran';
            $kategori = Kategori::where('nama_kategori', $validated['category'])->first();
            if (!$kategori) {
                $kategori = Kategori::create([
                    'nama_kategori' => $validated['category'],
                    'tipe' => $jenisTransaksi,
                ]);
            }

            // Create TransaksiKas
            $transaction = TransaksiKas::create([
                'kode_kelas' => $bendahara->kode_kelas,
                'id_bendahara' => $bendahara->id,
                'id_kategori' => $kategori->id,
                'jenis_transaksi' => $jenisTransaksi,
                'total_nominal' => $validated['amount'],
                'tanggal_transaksi' => $validated['transaction_date'],
                'keterangan' => $description,
            ]);

            // Auto-checklist / allocation logic if Category is Uang Kas
            if ($validated['type'] === 'income' && $validated['category'] === 'Uang Kas' && $studentId) {
                $this->allocateKasPayment($transaction, $studentId, $validated['amount'], $kelas);
            }

            // Create Log for creation
            $studentForLog = $studentId ? Siswa::find($studentId) : null;
            LogTransaksiKas::create([
                'id_transaksi_kas' => $transaction->id,
                'kode_kelas' => $bendahara->kode_kelas,
                'user_id' => auth()->id(),
                'aksi' => 'create',
                'alasan' => 'Pencatatan transaksi baru',
                'data_sesudahnya' => [
                    'keterangan' => $transaction->keterangan,
                    'jenis_transaksi' => $transaction->jenis_transaksi,
                    'total_nominal' => (float)$transaction->total_nominal,
                    'tanggal_transaksi' => Carbon::parse($transaction->tanggal_transaksi)->format('Y-m-d'),
                    'kategori' => $kategori->nama_kategori,
                    'student_name' => $studentForLog?->nama,
                ],
            ]);
        });

        return redirect()->route('bendahara.transactions.index')->with('success', 'Transaksi kas berhasil dicatat.');
    }

    public function edit($id)
    {
        $bendahara = auth()->user()->bendahara;
        $transaction = TransaksiKas::with(['kategori', 'detailTransaksi.siswa'])
            ->where('kode_kelas', $bendahara->kode_kelas)
            ->findOrFail($id);

        $students = Siswa::where('kode_kelas', $bendahara->kode_kelas)->orderBy('nama', 'asc')->get();

        $incomeCategories = Kategori::where('tipe', 'pemasukan')->pluck('nama_kategori')->values();
        if ($incomeCategories->isEmpty()) {
            $incomeCategories = collect(['Uang Kas', 'Sumbangan / Donasi', 'Lainnya (Pemasukan)']);
        }
        
        $expenseCategories = Kategori::where('tipe', 'pengeluaran')->pluck('nama_kategori')->values();
        if ($expenseCategories->isEmpty()) {
            $expenseCategories = collect(['Pembelian ATK', 'Kegiatan Kelas', 'Operasional Kelas', 'Lainnya (Pengeluaran)']);
        }

        // Get student id if this was a student fee payment
        $currentStudentId = $transaction->detailTransaksi->first()?->id_siswa;

        return view('bendahara.transactions.edit', compact('transaction', 'students', 'currentStudentId', 'incomeCategories', 'expenseCategories'));
    }

    public function update(Request $request, $id)
    {
        $bendahara = auth()->user()->bendahara;
        $transaction = TransaksiKas::with(['kategori', 'detailTransaksi'])
            ->where('kode_kelas', $bendahara->kode_kelas)
            ->findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'transaction_date' => 'required|date|before_or_equal:today',
            'category' => 'required|string',
            'student_id' => 'required_if:category,Uang Kas|nullable|exists:siswa,id',
            'reason' => 'required|string|max:255',
        ], [
            'transaction_date.before_or_equal' => 'Tanggal transaksi tidak boleh melebihi hari ini.',
            'reason.required' => 'Alasan perubahan transaksi wajib diisi untuk riwayat audit log.',
        ]);

        DB::transaction(function () use ($validated, $transaction, $bendahara) {
            $kelas = $bendahara->kelas;
            $jenisTransaksi = $validated['type'] === 'income' ? 'pemasukan' : 'pengeluaran';

            $kategori = Kategori::where('nama_kategori', $validated['category'])->first();
            if (!$kategori) {
                $kategori = Kategori::create([
                    'nama_kategori' => $validated['category'],
                    'tipe' => $jenisTransaksi,
                ]);
            }

            // Snapshot previous data for audit log
            $previousData = [
                'keterangan' => $transaction->keterangan,
                'jenis_transaksi' => $transaction->jenis_transaksi,
                'total_nominal' => (float)$transaction->total_nominal,
                'tanggal_transaksi' => Carbon::parse($transaction->tanggal_transaksi)->format('Y-m-d'),
                'kategori' => $transaction->kategori->nama_kategori,
                'student_id' => $transaction->detailTransaksi->first()?->id_siswa,
            ];

            // Delete old details if previously had them
            DetailTransaksiKas::where('id_transaksi_kas', $transaction->id)->delete();

            // Update main transaction
            $transaction->update([
                'id_kategori' => $kategori->id,
                'jenis_transaksi' => $validated['type'] === 'income' ? 'pemasukan' : 'pengeluaran',
                'total_nominal' => $validated['amount'],
                'tanggal_transaksi' => $validated['transaction_date'],
                'keterangan' => $validated['description'],
            ]);

            // Reallocate kas payment if applicable
            $studentId = $validated['student_id'] ?? null;
            if ($validated['type'] === 'income' && $validated['category'] === 'Uang Kas' && $studentId) {
                $this->allocateKasPayment($transaction, $studentId, $validated['amount'], $kelas, false);
            }

            $newData = [
                'keterangan' => $transaction->keterangan,
                'jenis_transaksi' => $transaction->jenis_transaksi,
                'total_nominal' => (float)$transaction->total_nominal,
                'tanggal_transaksi' => Carbon::parse($transaction->tanggal_transaksi)->format('Y-m-d'),
                'kategori' => $kategori->nama_kategori,
                'student_id' => $studentId,
            ];

            // Record Log
            LogTransaksiKas::create([
                'id_transaksi_kas' => $transaction->id,
                'kode_kelas' => $bendahara->kode_kelas,
                'user_id' => auth()->id(),
                'aksi' => 'edit',
                'alasan' => $validated['reason'],
                'data_sebelumnya' => $previousData,
                'data_sesudahnya' => $newData,
            ]);
        });

        return redirect()->route('bendahara.transactions.index')->with('success', 'Transaksi kas berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $bendahara = auth()->user()->bendahara;
        $transaction = TransaksiKas::with(['kategori', 'detailTransaksi.siswa'])
            ->where('kode_kelas', $bendahara->kode_kelas)
            ->findOrFail($id);

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ], [
            'reason.required' => 'Alasan penghapusan transaksi wajib diisi untuk riwayat audit log.',
        ]);

        $reason = $validated['reason'];

        DB::transaction(function () use ($transaction, $bendahara, $reason) {
            // Snapshot previous data for audit log
            $previousData = [
                'id' => $transaction->id,
                'keterangan' => $transaction->keterangan,
                'jenis_transaksi' => $transaction->jenis_transaksi,
                'total_nominal' => (float)$transaction->total_nominal,
                'tanggal_transaksi' => Carbon::parse($transaction->tanggal_transaksi)->format('Y-m-d'),
                'kategori' => $transaction->kategori->nama_kategori ?? 'Lainnya',
                'student_name' => $transaction->detailTransaksi->first()?->siswa?->nama,
            ];

            // Record Log
            LogTransaksiKas::create([
                'id_transaksi_kas' => $transaction->id,
                'kode_kelas' => $bendahara->kode_kelas,
                'user_id' => auth()->id(),
                'aksi' => 'delete',
                'alasan' => $reason,
                'data_sebelumnya' => $previousData,
                'data_sesudahnya' => null,
            ]);

            // DetailTransaksiKas will be deleted via FK cascade or explicitly
            DetailTransaksiKas::where('id_transaksi_kas', $transaction->id)->delete();
            $transaction->delete();
        });

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Transaksi berhasil dihapus.']);
        }

        return redirect()->route('bendahara.transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function logs(Request $request)
    {
        $bendahara = auth()->user()->bendahara;

        if ($request->ajax()) {
            $logs = LogTransaksiKas::with('user')
                ->where('kode_kelas', $bendahara->kode_kelas);

            return DataTables::of($logs)
                ->addIndexColumn()
                ->filter(function ($query) use ($request) {
                    if ($search = $request->input('search.value')) {
                        $query->where(function ($q) use ($search) {
                            $q->where('alasan', 'like', "%{$search}%")
                              ->orWhere('aksi', 'like', "%{$search}%")
                              ->orWhere('data_sesudahnya', 'like', "%{$search}%")
                              ->orWhere('data_sebelumnya', 'like', "%{$search}%")
                              ->orWhereHas('user', function ($uq) use ($search) {
                                  $uq->where('name', 'like', "%{$search}%");
                              });
                        });
                    }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order)->orderBy('id', 'desc');
                })
                ->editColumn('created_at', function ($log) {
                    $dt = $log->created_at ? $log->created_at->setTimezone('Asia/Jakarta') : now();
                    return '<span class="text-xs font-medium text-gray-700">' . $dt->translatedFormat('d M Y') . '</span><br><span class="text-[11px] text-gray-400 font-mono">' . $dt->format('H:i') . ' WIB</span>';
                })
                ->addColumn('user_name', function ($log) {
                    return '<span class="text-xs font-semibold text-navy">' . e($log->user->name ?? 'Pengguna Dihapus') . '</span>';
                })
                ->addColumn('action_badge', function ($log) {
                    if ($log->aksi === 'create') {
                        return '<span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Dibuat</span>';
                    } elseif ($log->aksi === 'edit') {
                        return '<span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-amber-50 text-amber-700 border border-amber-200/60 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Diedit</span>';
                    } else {
                        return '<span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-red-50 text-red-600 border border-red-200/60 inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Dihapus</span>';
                    }
                })
                ->addColumn('details', function ($log) {
                    $html = '<div class="text-xs space-y-1">';

                    if ($log->aksi === 'create' && $log->data_sesudahnya) {
                        $d = $log->data_sesudahnya;
                        $isIncome = ($d['jenis_transaksi'] ?? '') === 'pemasukan';
                        $nominalClass = $isIncome ? 'text-status-lunas' : 'text-status-menunggak';
                        $prefix = $isIncome ? '+' : '-';
                        $nominal = 'Rp ' . number_format($d['total_nominal'] ?? 0, 0, ',', '.');
                        $desc = e($d['keterangan'] ?? '-');
                        $kategori = e($d['kategori'] ?? '-');
                        $student = !empty($d['student_name']) ? e($d['student_name']) : null;

                        $html .= '<div class="flex items-center gap-2 flex-wrap">';
                        $html .= '<span class="font-medium text-gray-800">' . $desc . '</span>';
                        $html .= '<span class="font-bold ' . $nominalClass . '">' . $prefix . $nominal . '</span>';
                        $html .= '</div>';

                        $html .= '<div class="text-[11px] text-gray-400 flex items-center gap-1.5 flex-wrap">';
                        $html .= '<span>' . ($isIncome ? 'Pemasukan' : 'Pengeluaran') . ' &bull; ' . $kategori . '</span>';
                        if ($student) {
                            $html .= '<span>&bull;</span><span class="inline-flex items-center gap-0.5 text-gray-500"><span class="material-symbols-outlined text-[12px]">person</span>' . $student . '</span>';
                        }
                        $html .= '</div>';

                    } elseif ($log->aksi === 'edit') {
                        if ($log->alasan) {
                            $html .= '<div><span class="text-gray-400 text-[11px]">Alasan:</span> <span class="font-medium text-gray-800">' . e($log->alasan) . '</span></div>';
                        }

                        $before = $log->data_sebelumnya ?? [];
                        $after  = $log->data_sesudahnya ?? [];

                        $fields = [
                            'total_nominal'    => 'Nominal',
                            'keterangan'       => 'Deskripsi',
                            'tanggal_transaksi'=> 'Tanggal',
                            'kategori'         => 'Kategori',
                            'jenis_transaksi'  => 'Jenis',
                        ];

                        $changes = [];
                        foreach ($fields as $key => $label) {
                            $oldVal = $before[$key] ?? null;
                            $newVal = $after[$key]  ?? null;

                            $oldCmp = is_numeric($oldVal) ? (float)$oldVal : $oldVal;
                            $newCmp = is_numeric($newVal) ? (float)$newVal : $newVal;

                            if ($oldCmp == $newCmp) continue;

                            if ($key === 'total_nominal') {
                                $oldDisp = 'Rp ' . number_format($oldVal ?? 0, 0, ',', '.');
                                $newDisp = 'Rp ' . number_format($newVal ?? 0, 0, ',', '.');
                            } elseif ($key === 'tanggal_transaksi') {
                                $oldDisp = $oldVal ? Carbon::parse($oldVal)->format('d/m/Y') : '-';
                                $newDisp = $newVal ? Carbon::parse($newVal)->format('d/m/Y') : '-';
                            } else {
                                $oldDisp = e($oldVal ?? '-');
                                $newDisp = e($newVal ?? '-');
                            }

                            $changes[] = '<span class="text-gray-400">' . $label . ':</span> <span class="line-through text-red-400">' . $oldDisp . '</span> &rarr; <span class="text-emerald-600 font-semibold">' . $newDisp . '</span>';
                        }

                        if (!empty($changes)) {
                            $html .= '<div class="text-[11px] text-gray-600 flex flex-wrap items-center gap-x-2.5 gap-y-0.5">' . implode('<span class="text-gray-300">&bull;</span>', $changes) . '</div>';
                        } elseif (!$log->alasan) {
                            $html .= '<span class="text-gray-400 text-[11px] italic">Data transaksi diperbarui</span>';
                        }

                    } elseif ($log->aksi === 'delete') {
                        if ($log->alasan) {
                            $html .= '<div><span class="text-gray-400 text-[11px]">Alasan:</span> <span class="font-medium text-gray-800">' . e($log->alasan) . '</span></div>';
                        }

                        if ($log->data_sebelumnya) {
                            $d = $log->data_sebelumnya;
                            $nominal = 'Rp ' . number_format($d['total_nominal'] ?? 0, 0, ',', '.');
                            $desc = e($d['keterangan'] ?? '-');
                            $jenis = ucfirst($d['jenis_transaksi'] ?? '-');
                            $html .= '<div class="text-[11px] text-gray-400 flex items-center gap-1.5 flex-wrap">';
                            $html .= '<span>Dihapus: <strong class="text-gray-600 font-medium">' . $desc . '</strong> (' . $nominal . ' &bull; ' . $jenis . ')</span>';
                            $html .= '</div>';
                        }
                    }

                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['created_at', 'user_name', 'action_badge', 'details'])
                ->make(true);
        }

        // Tandai seluruh log mutasi yang belum dibaca sebagai sudah dibaca
        LogTransaksiKas::where('kode_kelas', $bendahara->kode_kelas)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('bendahara.transactions.logs');
    }

    /**
     * Helper for allocating fee amounts across unpaid/partially-paid periods.
     */
    private function allocateKasPayment($transaction, $studentId, $amount, $kelas, bool $sendNotification = true)
    {
        $student = Siswa::where('id', $studentId)
            ->where('kode_kelas', $kelas->kode_kelas)
            ->firstOrFail();

        $nominalStandar = (float)($kelas->nominal_standar ?? 20000);
        $periods = $kelas ? $kelas->getPeriods() : [];

        $remainingToAllocate = (float)$amount;
        $completedPeriods = [];
        $partiallyPaidPeriods = [];

        foreach ($periods as $period) {
            if ($remainingToAllocate <= 0) {
                break;
            }

            // Check current sum already paid for this period (excluding this transaction)
            $alreadyPaid = (float)DetailTransaksiKas::where('id_siswa', $student->id)
                ->where('periode', $period)
                ->when($kelas->last_reset_at, function ($q) use ($kelas) {
                    $q->where('created_at', '>', $kelas->last_reset_at);
                })
                ->where('id_transaksi_kas', '!=', $transaction->id)
                ->sum('nominal');

            $needed = max(0.0, $nominalStandar - $alreadyPaid);

            if ($needed <= 0) {
                // Period is already fully paid
                continue;
            }

            // Allocate up to what is needed
            $allocation = min($remainingToAllocate, $needed);

            DetailTransaksiKas::create([
                'id_transaksi_kas' => $transaction->id,
                'id_siswa'         => $student->id,
                'periode'          => $period,
                'nominal'          => $allocation,
            ]);

            $remainingToAllocate -= $allocation;

            if (($alreadyPaid + $allocation) >= $nominalStandar) {
                $completedPeriods[] = $period;
            } else {
                $partiallyPaidPeriods[$period] = ($alreadyPaid + $allocation);
            }
        }

        // If there's still an amount remaining after all standard periods
        if ($remainingToAllocate > 0) {
            $lastPeriod = end($periods);
            DetailTransaksiKas::create([
                'id_transaksi_kas' => $transaction->id,
                'id_siswa'         => $student->id,
                'periode'          => $lastPeriod ?: null,
                'nominal'          => $remainingToAllocate,
            ]);
        }

        // Append period information to transaction description
        $infoParts = [];
        if (!empty($completedPeriods)) {
            $infoParts[] = implode(', ', $completedPeriods);
        }
        if (!empty($partiallyPaidPeriods)) {
            $parts = [];
            foreach ($partiallyPaidPeriods as $p => $pAmount) {
                $parts[] = "{$p}: Rp " . number_format($pAmount, 0, ',', '.') . " (cicil)";
            }
            $infoParts[] = implode(', ', $parts);
        }

        if (!empty($infoParts)) {
            $appendInfo = "(" . implode(', ', $infoParts) . ")";
            if (!str_contains($transaction->keterangan, $appendInfo)) {
                $transaction->update([
                    'keterangan' => rtrim($transaction->keterangan) . " " . $appendInfo,
                ]);
            }
        }

        // Notify student about cash payment
        if ($sendNotification) {
            $bendahara = auth()->user()->bendahara ?? $transaction->bendahara;
            if ($bendahara) {
                KasNotificationService::notifyStudentPayment(
                    $transaction,
                    $student,
                    $completedPeriods,
                    $partiallyPaidPeriods,
                    (float)$amount,
                    $bendahara
                );
            }
        }
    }
}
