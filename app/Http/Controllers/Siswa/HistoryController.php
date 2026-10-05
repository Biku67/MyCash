<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DetailTransaksiKas;
use App\Models\Kelas;

class HistoryController extends Controller
{
    /**
     * Display payment history and personal kas checklist matrix.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $student = $user->siswa;

        if (!$student) {
            return view('siswa.history.index', [
                'user' => $user,
                'student' => null,
                'students' => collect([]),
                'kelas' => null,
                'periods' => [],
                'feePeriodType' => 'bulanan',
                'feeAmount' => 0,
            ]);
        }

        $kelas = $student->kelas;
        $feePeriodType = $kelas->tipe_periode ?? 'bulanan';
        $feeAmount = (float)($kelas->nominal_standar ?? 20000);
        $periods = $kelas ? $kelas->getPeriods() : [];

        // Ambil detail transaksi kas milik siswa ini
        $query = DetailTransaksiKas::where('id_siswa', $student->id)
            ->whereNotNull('periode');
        if ($kelas && $kelas->last_reset_at) {
            $query->where('created_at', '>', $kelas->last_reset_at);
        }
        $transactions = $query->get();

        $periodTotals = $transactions->groupBy('periode')->map(fn($group) => (float)$group->sum('nominal'));

        $paidPeriods = [];
        $partialPeriods = [];
        foreach ($periods as $period) {
            $periodPaid = (float)$periodTotals->get($period, 0.0);

            if ($periodPaid >= $feeAmount) {
                $paidPeriods[] = $period;
            } elseif ($periodPaid > 0) {
                $partialPeriods[$period] = $periodPaid;
            }
        }

        $student->paid_periods = $paidPeriods;
        $student->partial_periods = $partialPeriods;
        $student->contributed = (float)$transactions->sum('nominal');

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

        $students = collect([$student]);

        return view('siswa.history.index', compact(
            'user',
            'student',
            'students',
            'kelas',
            'periods',
            'feePeriodType',
            'feeAmount'
        ));
    }
}
