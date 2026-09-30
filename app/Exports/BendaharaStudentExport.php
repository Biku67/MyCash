<?php

namespace App\Exports;

use App\Models\Siswa;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Enumerable;

class BendaharaStudentExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $kodeKelas;
    protected $kelas;
    protected $tahunAjaran;

    public function __construct($kodeKelas, ?string $tahunAjaran = null)
    {
        $this->kodeKelas = $kodeKelas;
        $this->kelas = Kelas::where('kode_kelas', $kodeKelas)->first();
        $this->tahunAjaran = $tahunAjaran ?: ($this->kelas->tahun_ajaran ?? '2025/2026');
    }

    public function collection(): Enumerable
    {
        $feePeriodType = $this->kelas->tipe_periode ?? 'bulanan';
        $feeAmount = (float)($this->kelas->nominal_standar ?? 20000);
        $totalPeriods = $this->kelas ? count($this->kelas->getPeriods($this->tahunAjaran)) : 12;
        $ta = $this->tahunAjaran;

        $students = Siswa::where('kode_kelas', $this->kodeKelas)
            ->with(['user', 'detailTransaksiKas' => function ($q) use ($ta) {
                $q->where(function ($sub) use ($ta) {
                    $sub->where('tahun_ajaran', $ta)
                        ->orWhereNull('tahun_ajaran');
                });
            }])
            ->orderBy('nama', 'asc')
            ->get();

        $no = 1;
        return $students->map(function ($student) use (&$no, $totalPeriods, $feeAmount) {
            $totalDibayar = (float)$student->detailTransaksiKas->sum('nominal');
            $totalTagihan = $totalPeriods * $feeAmount;
            $totalTunggakan = max(0.0, $totalTagihan - $totalDibayar);

            $status = 'Up to Date';
            if ($totalTunggakan > 0) {
                $status = ($totalDibayar > 0) ? 'Pending' : 'Overdue';
            }

            return [
                'no' => $no++,
                'nama' => $student->nama,
                'nis' => $student->nis ?? '-',
                'email' => $student->user->email ?? '-',
                'no_hp' => $student->no_hp ?? '-',
                'total_dibayar' => $totalDibayar,
                'total_tunggakan' => $totalTunggakan,
                'status' => $status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'NIS',
            'Email',
            'No. HP',
            'Total Dibayar (Rp)',
            'Total Tunggakan (Rp)',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1B4F72'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Data Siswa & Kas ' . ($this->kelas->nama_kelas ?? '');
    }
}
