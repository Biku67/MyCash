<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksiKas;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class StudentController extends Controller
{
    /**
     * Get the authenticated Wali Kelas profile.
     */
    private function getWaliKelas()
    {
        $waliKelas = auth()->user()->waliKelas;
        if (!$waliKelas) {
            abort(403, 'Profil wali kelas tidak ditemukan untuk akun ini.');
        }
        return $waliKelas;
    }

    /**
     * Get the active class managed by this Wali Kelas.
     */
    private function getKelas()
    {
        $waliKelas = $this->getWaliKelas();
        return $waliKelas->kelas()->first();
    }

    /**
     * Display student roster for Wali Kelas.
     */
    public function index(Request $request)
    {
        $waliKelas = $this->getWaliKelas();
        $kelas = $this->getKelas();

        if ($request->ajax()) {
            if (!$kelas) {
                return DataTables::of(collect([]))->make(true);
            }

            $students = Siswa::where('kode_kelas', $kelas->kode_kelas)
                ->with('user')
                ->orderBy('nama', 'asc');

            return DataTables::of($students)
                ->addIndexColumn()
                ->addColumn('student_info', function ($student) {
                    $initial = strtoupper(substr($student->nama, 0, 1));
                    $email = $student->user->email ?? '-';
                    return '
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-navy/10 text-navy flex items-center justify-center font-bold text-xs">
                            ' . $initial . '
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 leading-tight">' . e($student->nama) . '</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">' . e($email) . '</p>
                        </div>
                    </div>';
                })
                ->addColumn('nis_badge', function ($student) {
                    return '<span class="font-mono text-xs text-gray-700 bg-gray-50 px-2.5 py-1 rounded-md border border-gray-200/60">' . e($student->nis ?? '-') . '</span>';
                })
                ->addColumn('contact', function ($student) {
                    return $student->no_hp ? e($student->no_hp) : '<span class="text-gray-400 italic text-xs">-</span>';
                })
                ->addColumn('actions', function ($student) {
                    $editUrl = route('wali-kelas.students.edit', $student->id);
                    $deleteUrl = route('wali-kelas.students.destroy', $student->id);
                    $resetUrl = route('wali-kelas.students.resetPassword', $student->id);
                    $nama = htmlspecialchars($student->nama, ENT_QUOTES, 'UTF-8');
                    $nis = htmlspecialchars($student->nis ?? '', ENT_QUOTES, 'UTF-8');

                    return '
                    <div class="flex items-center justify-end gap-1">
                        <form method="POST" action="' . $resetUrl . '" onsubmit="return confirmResetPasswordStudent(event, \'' . $nama . '\', \'' . $nis . '\')">
                            ' . csrf_field() . '
                            <button type="submit" class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Reset Password ke NIS (' . $nis . ')">
                                <span class="material-symbols-outlined text-lg">lock_reset</span>
                            </button>
                        </form>
                        <a href="' . $editUrl . '" class="p-1.5 text-gray-400 hover:text-navy hover:bg-navy/5 rounded-lg transition-colors" title="Edit Data Siswa">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </a>
                        <form method="POST" action="' . $deleteUrl . '" onsubmit="return confirmDeleteStudent(event, \'' . $nama . '\')">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Siswa">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </form>
                    </div>';
                })
                ->rawColumns(['student_info', 'nis_badge', 'contact', 'actions'])
                ->make(true);
        }

        $totalSiswa = $kelas ? Siswa::where('kode_kelas', $kelas->kode_kelas)->count() : 0;

        return view('wali-kelas.students.index', compact('waliKelas', 'kelas', 'totalSiswa'));
    }

    /**
     * Show form to register a new student.
     */
    public function create()
    {
        $kelas = $this->getKelas();
        if (!$kelas) {
            return redirect()->route('wali-kelas.students.index')->with('error', 'Anda belum ditugaskan mengampu kelas apapun.');
        }

        return view('wali-kelas.students.create', compact('kelas'));
    }

    /**
     * Store newly registered student and their user account.
     */
    public function store(Request $request)
    {
        $kelas = $this->getKelas();
        if (!$kelas) {
            return redirect()->route('wali-kelas.students.index')->with('error', 'Anda belum ditugaskan mengampu kelas apapun.');
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'nis'      => 'required|string|max:30|unique:siswa,nis',
            'email'    => 'nullable|string|email|max:255|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => 'nullable|string',
        ], [
            'name.required' => 'Nama lengkap siswa wajib diisi.',
            'nis.required'  => 'Nomor Induk Siswa (NIS) wajib diisi.',
            'nis.unique'    => 'NIS ini sudah terdaftar di sistem.',
            'email.unique'  => 'Email ini sudah digunakan oleh akun lain.',
        ]);

        $plainPassword = $request->filled('password') ? $request->input('password') : $validated['nis'];

        DB::transaction(function () use ($validated, $kelas, $plainPassword) {
            $email = $validated['email'] ?: ($validated['nis'] . '@siswa.mycash.id');

            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $email,
                'password'  => Hash::make($plainPassword),
                'role'      => 'siswa',
                'is_active' => true,
            ]);

            $user->assignRole('siswa');

            Siswa::create([
                'user_id'    => $user->id,
                'kode_kelas' => $kelas->kode_kelas,
                'nama'       => $validated['name'],
                'nis'        => $validated['nis'],
                'no_hp'      => $validated['phone'] ?? null,
            ]);
        });

        return redirect()->route('wali-kelas.students.index')->with('success', "Siswa {$validated['name']} berhasil didaftarkan ke kelas {$kelas->nama_kelas}. Password default akun login otomatis disetel ke NIS ({$validated['nis']}).");
    }

    /**
     * Show form to edit existing student data.
     */
    public function edit($id)
    {
        $kelas = $this->getKelas();
        if (!$kelas) {
            abort(403);
        }

        $student = Siswa::where('id', $id)
            ->where('kode_kelas', $kelas->kode_kelas)
            ->with('user')
            ->firstOrFail();

        return view('wali-kelas.students.edit', compact('student', 'kelas'));
    }

    /**
     * Update student and user record.
     */
    public function update(Request $request, $id)
    {
        $kelas = $this->getKelas();
        if (!$kelas) {
            abort(403);
        }

        $student = Siswa::where('id', $id)
            ->where('kode_kelas', $kelas->kode_kelas)
            ->firstOrFail();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'nis'   => 'required|string|max:30|unique:siswa,nis,' . $student->id,
            'email' => 'nullable|string|email|max:255|unique:users,email,' . ($student->user_id ?? 0),
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Nama siswa wajib diisi.',
            'nis.required'  => 'NIS wajib diisi.',
            'nis.unique'    => 'NIS ini sudah digunakan oleh siswa lain.',
            'email.unique'  => 'Email ini sudah digunakan oleh akun lain.',
        ]);

        DB::transaction(function () use ($validated, $student) {
            $student->update([
                'nama'  => $validated['name'],
                'nis'   => $validated['nis'],
                'no_hp' => $validated['phone'] ?? null,
            ]);

            if ($student->user) {
                $student->user->update([
                    'name'  => $validated['name'],
                    'email' => $validated['email'] ?: $student->user->email,
                ]);
            }
        });

        return redirect()->route('wali-kelas.students.index')->with('success', "Data siswa {$student->nama} berhasil diperbarui.");
    }

    /**
     * Delete student and associated records.
     */
    public function destroy($id)
    {
        $kelas = $this->getKelas();
        if (!$kelas) {
            abort(403);
        }

        $student = Siswa::where('id', $id)
            ->where('kode_kelas', $kelas->kode_kelas)
            ->firstOrFail();

        $nama = $student->nama;

        $txCount = DetailTransaksiKas::where('id_siswa', $student->id)->count();
        if ($txCount > 0) {
            return redirect()->route('wali-kelas.students.index')
                ->with('error', "Siswa {$nama} tidak dapat dihapus karena masih memiliki {$txCount} catatan transaksi kas.");
        }

        DB::transaction(function () use ($student) {
            $user = $student->user;
            $student->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('wali-kelas.students.index')->with('success', "Siswa {$nama} dan akun login berhasil dihapus.");
    }

    /**
     * Reset student password to their NIS.
     */
    public function resetPassword(Request $request, $id)
    {
        $kelas = $this->getKelas();
        if (!$kelas) {
            abort(403, 'Akses ditolak. Anda belum ditugaskan mengampu kelas.');
        }

        $student = Siswa::where('id', $id)
            ->where('kode_kelas', $kelas->kode_kelas)
            ->with('user')
            ->firstOrFail();

        if (!$student->user) {
            return redirect()->back()->with('error', "Akun login pengguna untuk siswa {$student->nama} tidak ditemukan.");
        }

        $student->user->update([
            'password' => Hash::make($student->nis),
        ]);

        return redirect()->back()->with('success', "Password akun siswa {$student->nama} berhasil direset ke nomor NIS ({$student->nis}).");
    }
}
