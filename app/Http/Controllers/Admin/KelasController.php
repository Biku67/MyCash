<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\WaliKelas;
use Yajra\DataTables\Facades\DataTables;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $kelasList = Kelas::with(['waliKelas', 'bendahara', 'siswa'])
                ->select('kelas.*');

            return DataTables::of($kelasList)
                ->addIndexColumn()
                ->addColumn('kelas_info', function ($k) {
                    $nama = e($k->nama_kelas);
                    $kode = e($k->kode_kelas);
                    return '
                    <div>
                        <p class="font-semibold text-gray-900 text-xs sm:text-sm">' . $nama . '</p>
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-navy/10 text-navy mt-0.5">' . $kode . '</span>
                    </div>';
                })
                ->addColumn('wali_kelas_name', function ($k) {
                    if ($k->waliKelas) {
                        return '
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-base text-gray-400">person</span>
                            <div>
                                <span class="font-medium text-gray-800 text-xs sm:text-sm">' . e($k->waliKelas->nama) . '</span>
                                <p class="text-[10px] text-gray-400 font-mono">' . ($k->waliKelas->nip ? 'NIP: ' . e($k->waliKelas->nip) : '-') . '</p>
                            </div>
                        </div>';
                    }
                    return '<span class="text-xs text-gray-400 italic">Belum ditentukan</span>';
                })
                ->addColumn('bendahara_count', function ($k) {
                    $count = $k->bendahara->count();
                    $names = $k->bendahara->pluck('nama')->implode(', ');
                    if ($count > 0) {
                        return '
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-accent/15 text-teal-accent-dark">' . $count . ' Bendahara</span>
                            <p class="text-[11px] text-gray-400 truncate max-w-[150px] mt-0.5" title="' . e($names) . '">' . e($names) . '</p>
                        </div>';
                    }
                    return '<span class="text-xs text-gray-400 italic">Belum ada</span>';
                })
                ->addColumn('siswa_count', function ($k) {
                    $count = $k->siswa->count();
                    return '<span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">' . $count . ' Siswa</span>';
                })
                ->addColumn('fee_parameters', function ($k) {
                    $tipe = $k->tipe_periode === 'mingguan'
                        ? 'Mingguan (M1-M24)'
                        : 'Bulanan (' . ($k->bulan_mulai ?? 'Jan') . ' - ' . ($k->bulan_selesai ?? 'Des') . ')';
                    $nominal = 'Rp ' . number_format($k->nominal_standar, 0, ',', '.');
                    return '
                    <div class="text-xs">
                        <span class="font-semibold text-gray-800">' . $nominal . '</span>
                        <p class="text-[10px] text-gray-400">' . $tipe . '</p>
                    </div>';
                })
                ->addColumn('actions', function ($k) {
                    $editUrl = route('admin.kelas.edit', $k->id);
                    $deleteUrl = route('admin.kelas.destroy', $k->id);
                    $nama = htmlspecialchars($k->nama_kelas, ENT_QUOTES, 'UTF-8');

                    return '
                    <div class="flex items-center justify-end gap-1">
                        <a href="' . $editUrl . '" class="p-1.5 text-gray-400 hover:text-navy hover:bg-navy/5 rounded-lg transition-colors" title="Edit Kelas">
                            <span class="material-symbols-outlined text-lg">edit</span>
                        </a>
                        <form method="POST" action="' . $deleteUrl . '" onsubmit="return confirmDeleteKelas(event, \'' . $nama . '\')">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Kelas">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </form>
                    </div>';
                })
                ->rawColumns(['kelas_info', 'wali_kelas_name', 'bendahara_count', 'siswa_count', 'fee_parameters', 'actions'])
                ->make(true);
        }

        $totalKelas = Kelas::count();
        $totalSiswa = \App\Models\Siswa::count();
        $totalWaliKelas = WaliKelas::count();

        return view('admin.kelas.index', compact('totalKelas', 'totalSiswa', 'totalWaliKelas'));
    }

    public function create()
    {
        $waliKelasList = WaliKelas::orderBy('nama', 'asc')->get();
        return view('admin.kelas.create', compact('waliKelasList'));
    }

    public function store(Request $request)
    {   
        if (empty($request->kode_kelas) && $request->filled('nama_kelas')) {
            $request->merge(['kode_kelas' => Kelas::generateUniqueKodeKelas($request->nama_kelas)]);
        } elseif ($request->filled('kode_kelas')) {
            $request->merge(['kode_kelas' => strtoupper(trim((string)$request->kode_kelas))]);
        }

        $validated = $request->validate([
            'kode_kelas'      => 'required|string|max:30|unique:kelas,kode_kelas',
            'nama_kelas'      => 'required|string|max:100',
            'id_wali_kelas'   => 'nullable|exists:wali_kelas,id',
            'tipe_periode'    => 'required|in:mingguan,bulanan',
            'nominal_standar' => 'required|numeric|min:0',
        ], [
            'kode_kelas.unique' => 'Kode kelas ini sudah digunakan. Harap gunakan nama kelas lain atau tentukan kode yang unik.',
            'nominal_standar.min' => 'Nominal standar tidak boleh negatif.',
        ]);

        Kelas::create($validated);

        return redirect()->route('admin.kelas.index')->with('success', "Data Kelas {$validated['nama_kelas']} ({$validated['kode_kelas']}) berhasil ditambahkan.");
    }

    public function edit($id)
    {
        $kelas = Kelas::with('waliKelas')->findOrFail($id);
        $waliKelasList = WaliKelas::orderBy('nama', 'asc')->get();
        return view('admin.kelas.edit', compact('kelas', 'waliKelasList'));
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        if (empty($request->kode_kelas) && $request->filled('nama_kelas')) {
            $request->merge(['kode_kelas' => Kelas::generateKodeKelas($request->nama_kelas)]);
        } else {
            $request->merge(['kode_kelas' => strtoupper(trim((string)$request->kode_kelas))]);
        }

        $validated = $request->validate([
            'kode_kelas'      => 'required|string|max:30|unique:kelas,kode_kelas,' . $kelas->id,
            'nama_kelas'      => 'required|string|max:100',
            'id_wali_kelas'   => 'nullable|exists:wali_kelas,id',
            'tipe_periode'    => 'required|in:mingguan,bulanan',
            'nominal_standar' => 'required|numeric|min:0',
        ], [
            'kode_kelas.unique' => 'Kode kelas ini sudah digunakan oleh kelas lain.',
            'nominal_standar.min' => 'Nominal standar tidak boleh negatif.',
        ]);

        $oldKode = $kelas->kode_kelas;
        $newKode = $validated['kode_kelas'];

        if ($oldKode !== $newKode) {
            \DB::transaction(function () use ($kelas, $validated, $oldKode, $newKode) {
                // 1. Create a temporary bridge key
                $tempKode = 'TMP_' . strtoupper(substr(md5(uniqid()), 0, 10));
                \DB::table('kelas')->insert([
                    'kode_kelas'      => $tempKode,
                    'nama_kelas'      => 'BRIDGE_TEMP',
                    'tipe_periode'    => $kelas->tipe_periode,
                    'nominal_standar' => $kelas->nominal_standar,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                // 2. Point all children to temp bridge key
                \App\Models\Siswa::where('kode_kelas', $oldKode)->update(['kode_kelas' => $tempKode]);
                \App\Models\Bendahara::where('kode_kelas', $oldKode)->update(['kode_kelas' => $tempKode]);
                \App\Models\TransaksiKas::where('kode_kelas', $oldKode)->update(['kode_kelas' => $tempKode]);
                \App\Models\Pengumuman::where('kode_kelas', $oldKode)->update(['kode_kelas' => $tempKode]);

                // 3. Update original kelas to new code (preserves original ID)
                $kelas->update($validated);

                // 4. Point children from bridge key to new code
                \App\Models\Siswa::where('kode_kelas', $tempKode)->update(['kode_kelas' => $newKode]);
                \App\Models\Bendahara::where('kode_kelas', $tempKode)->update(['kode_kelas' => $newKode]);
                \App\Models\TransaksiKas::where('kode_kelas', $tempKode)->update(['kode_kelas' => $newKode]);
                \App\Models\Pengumuman::where('kode_kelas', $tempKode)->update(['kode_kelas' => $newKode]);

                // 5. Clean up temporary bridge row
                \DB::table('kelas')->where('kode_kelas', $tempKode)->delete();
            });
        } else {
            $kelas->update($validated);
        }

        return redirect()->route('admin.kelas.index')->with('success', 'Data Kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kelas = Kelas::withCount(['siswa', 'bendahara', 'transaksiKas'])->findOrFail($id);

        if ($kelas->siswa_count > 0 || $kelas->bendahara_count > 0 || $kelas->transaksi_kas_count > 0) {
            $details = [];
            if ($kelas->siswa_count > 0) $details[] = "{$kelas->siswa_count} data siswa";
            if ($kelas->bendahara_count > 0) $details[] = "{$kelas->bendahara_count} akun bendahara";
            if ($kelas->transaksi_kas_count > 0) $details[] = "{$kelas->transaksi_kas_count} riwayat transaksi kas";
            $detailStr = implode(', ', $details);

            return redirect()->route('admin.kelas.index')->with('error', "Kelas {$kelas->nama_kelas} tidak dapat dihapus karena masih memiliki {$detailStr}.");
        }

        $kelas->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Data Kelas berhasil dihapus.');
    }
}
