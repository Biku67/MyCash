<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\TransaksiKas;
use App\Models\DetailTransaksiKas;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the wali kelas dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $waliKelas = $user->waliKelas;
        $kelasList = $waliKelas ? $waliKelas->kelas()->with(['siswa', 'bendahara'])->get() : collect();

        $totalSiswa = 0;
        $totalMasuk = 0;
        $totalKeluar = 0;
        $totalKasTerkumpul = 0;

        $kodeKelasArray = $kelasList->pluck('kode_kelas')->toArray();
        if (!empty($kodeKelasArray)) {
            $totalMasuk = (float)TransaksiKas::whereIn('kode_kelas', $kodeKelasArray)
                ->where('jenis_transaksi', 'pemasukan')
                ->sum('total_nominal');

            $totalKeluar = (float)TransaksiKas::whereIn('kode_kelas', $kodeKelasArray)
                ->where('jenis_transaksi', 'pengeluaran')
                ->sum('total_nominal');

            $totalKasTerkumpul = max(0, $totalMasuk - $totalKeluar);
        }

        // Hitung statistik per kelas
        $kelasListWithStats = $kelasList->map(function ($k) {
            $in = (float)TransaksiKas::where('kode_kelas', $k->kode_kelas)->where('jenis_transaksi', 'pemasukan')->sum('total_nominal');
            $out = (float)TransaksiKas::where('kode_kelas', $k->kode_kelas)->where('jenis_transaksi', 'pengeluaran')->sum('total_nominal');
            $k->total_masuk = $in;
            $k->total_keluar = $out;
            $k->saldo_kas = max(0, $in - $out);
            $k->total_siswa = $k->siswa->count();
            return $k;
        });

        foreach ($kelasList as $k) {
            $totalSiswa += $k->siswa->count();
        }

        // Kepatuhan Iuran Siswa
        $allSiswaIds = [];
        foreach ($kelasList as $k) {
            foreach ($k->siswa as $s) {
                $allSiswaIds[] = $s->id;
            }
        }

        $siswaPaidCount = !empty($allSiswaIds) 
            ? DetailTransaksiKas::whereIn('id_siswa', $allSiswaIds)->distinct('id_siswa')->count('id_siswa') 
            : 0;
        $siswaUnpaidCount = max(0, $totalSiswa - $siswaPaidCount);
        $complianceRate = $totalSiswa > 0 ? round(($siswaPaidCount / $totalSiswa) * 100) : 0;

        // 5 Transaksi Terbaru di Kelas Binaan
        $recentTransactions = !empty($kodeKelasArray)
            ? TransaksiKas::whereIn('kode_kelas', $kodeKelasArray)
                ->with(['kelas', 'kategori', 'bendahara'])
                ->latest('tanggal_transaksi')
                ->latest('id')
                ->take(5)
                ->get()
            : collect();

        return view('wali-kelas.dashboard', compact(
            'user',
            'waliKelas',
            'kelasListWithStats',
            'totalSiswa',
            'totalMasuk',
            'totalKeluar',
            'totalKasTerkumpul',
            'siswaPaidCount',
            'siswaUnpaidCount',
            'complianceRate',
            'recentTransactions'
        ));
    }
}
