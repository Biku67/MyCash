<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Bendahara;
use App\Models\WaliKelas;
use App\Models\TransaksiKas;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the super admin dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // 5 Metrik Inti
        $totalKelas = Kelas::count();
        $totalSiswa = Siswa::count();
        $totalBendahara = Bendahara::count();
        $totalWaliKelas = WaliKelas::count();

        $totalMasukSekolah = (float)TransaksiKas::where('jenis_transaksi', 'pemasukan')->sum('total_nominal');
        $totalKeluarSekolah = (float)TransaksiKas::where('jenis_transaksi', 'pengeluaran')->sum('total_nominal');
        $totalSaldoSekolah = $totalMasukSekolah - $totalKeluarSekolah;

        // Rincian Kesehatan Kas Seluruh Kelas
        $rawKelas = Kelas::with(['waliKelas', 'bendahara', 'siswa'])->orderBy('nama_kelas')->get();
        $kelasList = $rawKelas->map(function ($k) {
            $masuk = (float)TransaksiKas::where('kode_kelas', $k->kode_kelas)->where('jenis_transaksi', 'pemasukan')->sum('total_nominal');
            $keluar = (float)TransaksiKas::where('kode_kelas', $k->kode_kelas)->where('jenis_transaksi', 'pengeluaran')->sum('total_nominal');
            $k->total_masuk = $masuk;
            $k->total_keluar = $keluar;
            $k->saldo_kas = $masuk - $keluar;
            $k->total_siswa = $k->siswa->count();
            return $k;
        });

        // Chart Data: Sebaran Saldo Kas Antar Kelas & Distribusi Siswa
        $chartClassNames = $kelasList->pluck('nama_kelas')->toArray();
        $chartClassBalances = $kelasList->pluck('saldo_kas')->toArray();
        $chartClassStudents = $kelasList->pluck('total_siswa')->toArray();

        // 5 Transaksi Terbaru Seluruh Kelas
        $recentTransactions = TransaksiKas::with(['kelas', 'kategori', 'bendahara'])
            ->latest('tanggal_transaksi')
            ->latest('id')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'user',
            'totalKelas',
            'totalSiswa',
            'totalBendahara',
            'totalWaliKelas',
            'totalMasukSekolah',
            'totalKeluarSekolah',
            'totalSaldoSekolah',
            'kelasList',
            'chartClassNames',
            'chartClassBalances',
            'chartClassStudents',
            'recentTransactions'
        ));
    }
}
