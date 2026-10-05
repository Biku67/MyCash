<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Kelas;

class ChecklistController extends Controller
{
    /**
     * Get the authenticated Wali Kelas profile.
     */
    private function getWaliKelas()
    {
        $waliKelas = auth()->user()->waliKelas;
        if (!$waliKelas) {
            abort(403, 'Akses ditolak. Anda belum terdaftar sebagai wali kelas.');
        }
        return $waliKelas;
    }

    /**
     * Display the student kas checklist matrix for Wali Kelas.
     */
    public function index(Request $request)
    {
        $waliKelas = $this->getWaliKelas();
        $kelasList = $waliKelas->kelas()->get();

        if ($kelasList->isEmpty()) {
            return view('wali-kelas.checklist.index', [
                'kelasList' => collect(),
                'kelas' => null,
                'students' => collect(),
                'periods' => [],
                'feePeriodType' => 'bulanan',
                'feeAmount' => 0,
                'monthList' => Kelas::getMonthList(),
                'selectedKodeKelas' => null,
            ]);
        }

        $selectedKodeKelas = $request->input('kode_kelas', $kelasList->first()->kode_kelas);
        $kelas = $kelasList->where('kode_kelas', $selectedKodeKelas)->first() ?? $kelasList->first();

        $feePeriodType = $kelas->tipe_periode ?? 'bulanan';
        $feeAmount = (float)($kelas->nominal_standar ?? 20000);
        $monthList = Kelas::getMonthList();
        $periods = $kelas->getPeriods();

        $students = Siswa::where('kode_kelas', $kelas->kode_kelas)
            ->with(['detailTransaksiKas' => function ($query) use ($kelas) {
                if ($kelas->last_reset_at) {
                    $query->where('created_at', '>', $kelas->last_reset_at);
                }
            }, 'user'])
            ->orderBy('nama', 'asc')
            ->get()
            ->map(function ($student) use ($periods, $feeAmount) {
                $periodTotals = $student->detailTransaksiKas
                    ->whereNotNull('periode')
                    ->groupBy('periode')
                    ->map(fn($group) => (float)$group->sum('nominal'));

                $paidPeriods = [];
                $partialPeriods = [];
                foreach ($periods as $period) {
                    $periodPaid = $periodTotals->get($period, 0.0);
                    if ($periodPaid >= $feeAmount) {
                        $paidPeriods[] = $period;
                    } elseif ($periodPaid > 0) {
                        $partialPeriods[$period] = $periodPaid;
                    }
                }

                $student->paid_periods = $paidPeriods;
                $student->partial_periods = $partialPeriods;
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

        return view('wali-kelas.checklist.index', compact(
            'students', 'periods', 'feePeriodType', 'feeAmount', 'kelas', 'monthList', 'kelasList', 'selectedKodeKelas'
        ));
    }
}
