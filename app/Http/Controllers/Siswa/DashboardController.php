<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $student = $user->siswa;
        $kelas = $student ? $student->kelas : null;
        $totalPaid = $student ? (float)$student->detailTransaksiKas()->sum('nominal') : 0;

        $latestNotification = $student 
            ? $student->pengumumanPenerima()->with('pengumuman.bendahara.user')->latest()->first() 
            : null;

        return view('siswa.dashboard', compact('user', 'student', 'kelas', 'totalPaid', 'latestNotification'));
    }
}
