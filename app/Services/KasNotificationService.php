<?php

namespace App\Services;

use App\Models\Bendahara;
use App\Models\Pengumuman;
use App\Models\PengumumanPenerima;
use App\Models\Siswa;
use App\Models\TransaksiKas;
use Carbon\Carbon;

class KasNotificationService
{
    /**
     * Map abbreviation to Indonesian full month name.
     */
    const MONTH_MAP = [
        'Jan' => 'Januari',
        'Feb' => 'Februari',
        'Mar' => 'Maret',
        'Apr' => 'April',
        'Mei' => 'Mei',
        'Jun' => 'Juni',
        'Jul' => 'Juli',
        'Agt' => 'Agustus',
        'Sep' => 'September',
        'Okt' => 'Oktober',
        'Nov' => 'November',
        'Des' => 'Desember',
    ];

    /**
     * Format a period identifier into human-readable Indonesian text.
     *
     * @param string $period
     * @return string
     */
    public static function formatPeriodName(string $period): string
    {
        if (isset(self::MONTH_MAP[$period])) {
            return self::MONTH_MAP[$period];
        }

        // Weekly period e.g. M1, M2 -> Minggu ke-1, Minggu ke-2
        if (preg_match('/^M(\d+)$/i', $period, $matches)) {
            return 'Minggu ke-' . $matches[1];
        }

        return $period;
    }

    /**
     * Build human-readable string for periods paid.
     *
     * @param array $completedPeriods
     * @param array $partiallyPaidPeriods
     * @return string
     */
    public static function buildPeriodsDescription(array $completedPeriods = [], array $partiallyPaidPeriods = []): string
    {
        $parts = [];

        foreach ($completedPeriods as $p) {
            $parts[] = self::formatPeriodName($p);
        }

        foreach ($partiallyPaidPeriods as $p => $pAmount) {
            $pName = self::formatPeriodName((string)$p);
            $formattedPartial = number_format((float)$pAmount, 0, ',', '.');
            $parts[] = "{$pName} (cicil Rp {$formattedPartial})";
        }

        if (empty($parts)) {
            return 'Uang Kas';
        }

        return implode(', ', $parts);
    }

    /**
     * Send payment notification to a specific student when bendahara enters a cash payment.
     *
     * @param TransaksiKas $transaction
     * @param Siswa $student
     * @param array $completedPeriods
     * @param array $partiallyPaidPeriods
     * @param float $amount
     * @param Bendahara $bendahara
     * @return Pengumuman
     */
    public static function notifyStudentPayment(
        TransaksiKas $transaction,
        Siswa $student,
        array $completedPeriods,
        array $partiallyPaidPeriods,
        float $amount,
        Bendahara $bendahara
    ): Pengumuman {
        $periodString = self::buildPeriodsDescription($completedPeriods, $partiallyPaidPeriods);
        $formattedAmount = 'Rp ' . number_format($amount, 0, ',', '.');
        $notifDate = now()->translatedFormat('l, d F Y - H:i') . ' WIB';
        $txDate = Carbon::parse($transaction->tanggal_transaksi)->translatedFormat('d F Y');
        $bendaharaName = $bendahara->nama ?? ($bendahara->user->name ?? 'Bendahara Kelas');

        $judul = "Pembayaran Kas Berhasil: {$formattedAmount} ({$periodString})";
        if (mb_strlen($judul) > 250) {
            $judul = "Pembayaran Kas Berhasil: {$formattedAmount}";
        }

        $isi = "Halo {$student->nama},\n\n"
             . "Pembayaran uang kas Anda telah berhasil dicatat oleh Bendahara Kelas ({$bendaharaName}).\n\n"
             . "Rincian Pembayaran Kas:\n"
             . "• Nominal Dibayar : {$formattedAmount}\n"
             . "• Untuk Bulan/Periode : {$periodString}\n"
             . "• Tanggal Transaksi : {$txDate}\n"
             . "• Tanggal Notifikasi : {$notifDate}\n"
             . "• Keterangan : " . ($transaction->keterangan ?? '-') . "\n\n"
             . "Status kas Anda telah diperbarui secara otomatis. Terima kasih atas kedisiplinan dan kontribusi Anda!";

        $announcement = Pengumuman::create([
            'kode_kelas'   => $bendahara->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'judul'        => $judul,
            'isi'          => $isi,
        ]);

        PengumumanPenerima::create([
            'id_pengumuman' => $announcement->id,
            'id_siswa'      => $student->id,
            'is_read'       => false,
            'read_at'       => null,
        ]);

        return $announcement;
    }
}
