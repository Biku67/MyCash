<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WaliKelas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class WaliKelasController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $waliKelases = WaliKelas::with(['user', 'kelas'])
                ->select('wali_kelas.*');

            return DataTables::of($waliKelases)
                ->addIndexColumn()
                ->addColumn('wali_info', function ($w) {
                    $initial = strtoupper(substr($w->nama, 0, 1));
                    $email = $w->user ? e($w->user->email) : '-';
                    $nama = e($w->nama);
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
                ->addColumn('nip_badge', function ($w) {
                    $nip = $w->nip ? e($w->nip) : '-';
                    return '<span class="font-mono text-xs text-gray-600 font-semibold">' . $nip . '</span>';
                })
                ->addColumn('phone_info', function ($w) {
                    $hp = $w->no_hp ? e($w->no_hp) : '-';
                    return '<span class="text-xs text-gray-600">' . $hp . '</span>';
                })
                ->addColumn('kelas_diampu', function ($w) {
                    if ($w->kelas->count() > 0) {
                        $html = '<div class="flex flex-wrap gap-1">';
                        foreach ($w->kelas as $k) {
                            $html .= '<span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-teal-accent/15 text-teal-accent-dark">' . e($k->nama_kelas) . '</span>';
                        }
                        $html .= '</div>';
                        return $html;
                    }
                    return '<span class="text-xs text-gray-400 italic">Belum mengampu kelas</span>';
                })
                ->addColumn('actions', function ($w) {
                    $editUrl = route('admin.wali-kelas.edit', $w->id);
                    $deleteUrl = route('admin.wali-kelas.destroy', $w->id);
                    $nama = htmlspecialchars($w->nama, ENT_QUOTES, 'UTF-8');

                    return '
                    <div class="flex items-center justify-end gap-1">
                        <a href="' . $editUrl . '" class="p-1.5 text-gray-400 hover:text-navy hover:bg-navy/5 rounded-lg transition-colors" title="Edit Wali Kelas">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </a>
                        <form method="POST" action="' . $deleteUrl . '" onsubmit="return confirmDeleteWaliKelas(event, \'' . $nama . '\')">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Wali Kelas">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </form>
                    </div>';
                })
                ->rawColumns(['wali_info', 'nip_badge', 'phone_info', 'kelas_diampu', 'actions'])
                ->make(true);
        }

        $totalWaliKelas = WaliKelas::count();
        $totalKelas = \App\Models\Kelas::count();

        return view('admin.wali-kelas.index', compact('totalWaliKelas', 'totalKelas'));
    }

    public function create()
    {
        return view('admin.wali-kelas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'nip'      => 'nullable|string|max:30|unique:wali_kelas,nip',
            'no_hp'    => 'nullable|string|max:20',
        ], [
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'nip.unique' => 'NIP ini sudah terdaftar untuk wali kelas lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
                'role'      => 'wali_kelas',
                'is_active' => true,
            ]);

            $user->assignRole('wali_kelas');

            WaliKelas::create([
                'user_id' => $user->id,
                'nama'    => $validated['name'],
                'nip'     => $validated['nip'] ?? null,
                'no_hp'   => $validated['no_hp'] ?? null,
            ]);
        });

        return redirect()->route('admin.wali-kelas.index')->with('success', 'Data Wali Kelas baru berhasil didaftarkan.');
    }

    public function edit($id)
    {
        $waliKelas = WaliKelas::with(['user', 'kelas'])->findOrFail($id);
        return view('admin.wali-kelas.edit', compact('waliKelas'));
    }

    public function update(Request $request, $id)
    {
        $waliKelas = WaliKelas::with('user')->findOrFail($id);
        $user = $waliKelas->user;

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'nip'      => 'nullable|string|max:30|unique:wali_kelas,nip,' . $id,
            'no_hp'    => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'nip.unique' => 'NIP ini sudah terdaftar untuk wali kelas lain.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal 8 karakter.',
        ]);

        DB::transaction(function () use ($validated, $waliKelas, $user) {
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();

            $waliKelas->update([
                'nama'  => $validated['name'],
                'nip'   => $validated['nip'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
            ]);
        });

        return redirect()->route('admin.wali-kelas.index')->with('success', 'Data Wali Kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $waliKelas = WaliKelas::with(['user', 'kelas'])->findOrFail($id);

        if ($waliKelas->kelas->count() > 0) {
            $namaKelas = $waliKelas->kelas->pluck('nama_kelas')->join(', ');
            return redirect()->route('admin.wali-kelas.index')
                ->with('error', "Wali Kelas {$waliKelas->nama} tidak dapat dihapus karena masih aktif ditugaskan pada kelas {$namaKelas}. Harap alihkan penugasan kelas terlebih dahulu.");
        }

        DB::transaction(function () use ($waliKelas) {
            $user = $waliKelas->user;
            $waliKelas->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('admin.wali-kelas.index')->with('success', 'Data Wali Kelas berhasil dihapus.');
    }
}
