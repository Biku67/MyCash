<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\TransaksiKas;
use App\Models\DetailTransaksiKas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the bendahara dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $bendahara = $user->bendahara;
        $kelas = $bendahara ? $bendahara->kelas : null;

        $totalSiswa = 0;
        $saldoKas = 0;
        $totalMasuk = 0;
        $totalKeluar = 0;
        $masukBulanIni = 0;
        $keluarBulanIni = 0;
        $siswaLunasCount = 0;
        $persentaseLunas = 0;

        $months = [];
        $chartIncome = [];
        $chartExpense = [];
        $recentTransactions = collect();

        if ($kelas) {
            $totalSiswa = $kelas->siswa()->count();

            // Total akumulasi
            $totalMasuk = (float)TransaksiKas::where('kode_kelas', $kelas->kode_kelas)
                ->where('jenis_transaksi', 'pemasukan')
                ->sum('total_nominal');

            $totalKeluar = (float)TransaksiKas::where('kode_kelas', $kelas->kode_kelas)
                ->where('jenis_transaksi', 'pengeluaran')
                ->sum('total_nominal');

            $saldoKas = max(0, $totalMasuk - $totalKeluar);

            // Bulan Ini
            $startOfMonth = now()->startOfMonth()->toDateString();
            $endOfMonth = now()->endOfMonth()->toDateString();

            $masukBulanIni = (float)TransaksiKas::where('kode_kelas', $kelas->kode_kelas)
                ->where('jenis_transaksi', 'pemasukan')
                ->whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])
                ->sum('total_nominal');

            $keluarBulanIni = (float)TransaksiKas::where('kode_kelas', $kelas->kode_kelas)
                ->where('jenis_transaksi', 'pengeluaran')
                ->whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])
                ->sum('total_nominal');

            // Siswa berpartisipasi/lunas kas
            $siswaIds = $kelas->siswa->pluck('id');
            $siswaPaidCount = DetailTransaksiKas::whereIn('id_siswa', $siswaIds)->distinct('id_siswa')->count('id_siswa');
            $siswaLunasCount = $siswaPaidCount;
            $persentaseLunas = $totalSiswa > 0 ? round(($siswaLunasCount / $totalSiswa) * 100) : 0;

            // Tren 6 Bulan Terakhir untuk ApexCharts
            for ($i = 5; $i >= 0; $i--) {
                $targetMonth = now()->subMonths($i);
                $months[] = $targetMonth->translatedFormat('M Y');
                $startM = $targetMonth->copy()->startOfMonth()->toDateString();
                $endM = $targetMonth->copy()->endOfMonth()->toDateString();

                $inc = (float)TransaksiKas::where('kode_kelas', $kelas->kode_kelas)
                    ->where('jenis_transaksi', 'pemasukan')
                    ->whereBetween('tanggal_transaksi', [$startM, $endM])
                    ->sum('total_nominal');

                $exp = (float)TransaksiKas::where('kode_kelas', $kelas->kode_kelas)
                    ->where('jenis_transaksi', 'pengeluaran')
                    ->whereBetween('tanggal_transaksi', [$startM, $endM])
                    ->sum('total_nominal');

                $chartIncome[] = $inc;
                $chartExpense[] = $exp;
            }

            // 5 Transaksi Terbaru
            $recentTransactions = TransaksiKas::where('kode_kelas', $kelas->kode_kelas)
                ->with('kategori')
                ->latest('tanggal_transaksi')
                ->latest('id')
                ->take(5)
                ->get();
        }

        return view('bendahara.dashboard', compact(
            'user',
            'bendahara',
            'kelas',
            'totalSiswa',
            'saldoKas',
            'totalMasuk',
            'totalKeluar',
            'masukBulanIni',
            'keluarBulanIni',
            'siswaLunasCount',
            'persentaseLunas',
            'months',
            'chartIncome',
            'chartExpense',
            'recentTransactions'
        ));
    }
}
