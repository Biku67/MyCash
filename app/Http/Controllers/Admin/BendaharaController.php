<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Bendahara;
use App\Models\Kelas;
use App\Exports\AdminBendaharaExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BendaharaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $bendaharas = Bendahara::with(['user', 'kelas.siswa'])
                ->select('bendahara.*');

            return DataTables::of($bendaharas)
                ->addIndexColumn()
                ->addColumn('bendahara_info', function ($b) {
                    $initial = strtoupper(substr($b->nama, 0, 1));
                    $email = $b->user ? e($b->user->email) : '-';
                    $nama = e($b->nama);
                    return '
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-navy/10 text-navy flex items-center justify-center font-bold text-xs flex-shrink-0">
                            ' . $initial . '
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 text-xs sm:text-sm leading-tight truncate">' . $nama . '</p>
                            <p class="text-[11px] text-gray-400 truncate mt-0.5">' . $email . '</p>
                        </div>
                    </div>';
                })
                ->addColumn('kelas_badge', function ($b) {
                    $namaKelas = $b->kelas ? e($b->kelas->nama_kelas) : e($b->kode_kelas);
                    $kodeKelas = e($b->kode_kelas);
                    return '
                    <div>
                        <span class="font-semibold text-gray-800 text-xs sm:text-sm">' . $namaKelas . '</span>
                        <span class="block text-[10px] text-gray-400 font-mono">' . $kodeKelas . '</span>
                    </div>';
                })
                ->addColumn('contact_info', function ($b) {
                    $nis = $b->nis ? ('NIS: ' . e($b->nis)) : '-';
                    $hp = $b->no_hp ? e($b->no_hp) : '-';
                    return '
                    <div class="text-xs text-gray-600">
                        <p class="font-mono">' . $nis . '</p>
                        <p class="text-[11px] text-gray-400">' . $hp . '</p>
                    </div>';
                })
                ->addColumn('students_count', function ($b) {
                    $count = $b->kelas ? $b->kelas->siswa->count() : 0;
                    return '<span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">' . $count . ' Siswa</span>';
                })
                ->addColumn('actions', function ($b) {
                    $editUrl = route('admin.bendahara.edit', $b->id);
                    $deleteUrl = route('admin.bendahara.destroy', $b->id);
                    $nama = htmlspecialchars($b->nama, ENT_QUOTES, 'UTF-8');

                    return '
                    <div class="flex items-center justify-end gap-1">
                        <a href="' . $editUrl . '" class="p-1.5 text-gray-400 hover:text-navy hover:bg-navy/5 rounded-lg transition-colors" title="Edit Bendahara">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </a>
                        <form method="POST" action="' . $deleteUrl . '" onsubmit="return confirmDeleteBendahara(event, \'' . $nama . '\')">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Bendahara">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </form>
                    </div>';
                })
                ->rawColumns(['bendahara_info', 'kelas_badge', 'contact_info', 'students_count', 'actions'])
                ->make(true);
        }

        $totalBendahara = Bendahara::count();
        $totalKelas = Kelas::count();

        return view('admin.bendahara.index', compact('totalBendahara', 'totalKelas'));
    }

    public function create()
    {
        $kelasList = Kelas::orderBy('nama_kelas', 'asc')->get();
        return view('admin.bendahara.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email',
            'password'   => 'required|string|min:8|confirmed',
            'kode_kelas' => 'required|string|exists:kelas,kode_kelas',
            'nis'        => 'nullable|string|max:30',
            'no_hp'      => 'nullable|string|max:20',
        ], [
            'email.unique' => 'Email ini sudah terdaftar untuk pengguna lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'kode_kelas.exists' => 'Kelas yang dipilih tidak valid.',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
                'role'      => 'bendahara',
                'is_active' => true,
            ]);

            $user->assignRole('bendahara');

            Bendahara::create([
                'user_id'    => $user->id,
                'kode_kelas' => $validated['kode_kelas'],
                'nama'       => $validated['name'],
                'nis'        => $validated['nis'] ?? null,
                'no_hp'      => $validated['no_hp'] ?? null,
            ]);
        });

        return redirect()->route('admin.bendahara.index')->with('success', 'Akun Bendahara baru berhasil dibuat.');
    }

    public function edit($id)
    {
        $bendahara = Bendahara::with(['user', 'kelas'])->findOrFail($id);
        $kelasList = Kelas::orderBy('nama_kelas', 'asc')->get();
        return view('admin.bendahara.edit', compact('bendahara', 'kelasList'));
    }

    public function update(Request $request, $id)
    {
        $bendahara = Bendahara::with('user')->findOrFail($id);
        $user = $bendahara->user;

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email,' . $user->id,
            'kode_kelas' => 'required|string|exists:kelas,kode_kelas',
            'nis'        => 'nullable|string|max:30',
            'no_hp'      => 'nullable|string|max:20',
            'password'   => 'nullable|string|min:8|confirmed',
        ], [
            'email.unique' => 'Email ini sudah terdaftar untuk pengguna lain.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'kode_kelas.exists' => 'Kelas yang dipilih tidak valid.',
        ]);

        DB::transaction(function () use ($validated, $bendahara, $user) {
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();

            $bendahara->update([
                'nama'       => $validated['name'],
                'kode_kelas' => $validated['kode_kelas'],
                'nis'        => $validated['nis'] ?? null,
                'no_hp'      => $validated['no_hp'] ?? null,
            ]);
        });

        return redirect()->route('admin.bendahara.index')->with('success', 'Data Bendahara berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $bendahara = Bendahara::with('user')->withCount('transaksiKas')->findOrFail($id);

        if ($bendahara->transaksi_kas_count > 0) {
            return redirect()->route('admin.bendahara.index')->with('error', "Bendahara {$bendahara->nama} tidak dapat dihapus karena telah mencatat {$bendahara->transaksi_kas_count} transaksi kas.");
        }

        DB::transaction(function () use ($bendahara) {
            $user = $bendahara->user;
            $bendahara->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('admin.bendahara.index')->with('success', 'Akun Bendahara berhasil dihapus.');
    }

    public function exportExcel()
    {
        return Excel::download(
            new AdminBendaharaExport,
            'data-bendahara-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $bendaharas = Bendahara::with(['user', 'kelas.siswa'])->latest()->get();

        $headings = ['No', 'Nama Bendahara', 'Email', 'Kelas', 'NIS', 'No. HP', 'Siswa', 'Status'];
        $rows = [];
        $no = 1;

        foreach ($bendaharas as $b) {
            $rows[] = [
                $no++,
                $b->nama,
                $b->user->email ?? '-',
                $b->kelas ? $b->kelas->nama_kelas : $b->kode_kelas,
                $b->nis ?? '-',
                $b->no_hp ?? '-',
                $b->kelas ? $b->kelas->siswa->count() : 0,
                ($b->user && $b->user->is_active) ? 'Aktif' : 'Non-Aktif',
            ];
        }

        $pdf = Pdf::loadView('exports.pdf', [
            'title'    => 'Daftar Akun Bendahara Kelas',
            'headings' => $headings,
            'rows'     => $rows,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('data-bendahara-' . now()->format('Y-m-d') . '.pdf');
    }
}
