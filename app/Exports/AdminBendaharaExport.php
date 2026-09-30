<?php

namespace App\Exports;

use App\Models\Bendahara;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Enumerable;

class AdminBendaharaExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function collection(): Enumerable
    {
        $bendaharas = Bendahara::with(['user', 'kelas.siswa'])
            ->latest()
            ->get();

        $no = 1;
        return $bendaharas->map(function ($b) use (&$no) {
            return [
                'no' => $no++,
                'nama' => $b->nama,
                'email' => $b->user->email ?? '-',
                'kelas' => $b->kelas ? ($b->kelas->nama_kelas . ' (' . $b->kode_kelas . ')') : $b->kode_kelas,
                'nis' => $b->nis ?? '-',
                'no_hp' => $b->no_hp ?? '-',
                'total_siswa' => $b->kelas ? $b->kelas->siswa->count() : 0,
                'status' => ($b->user && $b->user->is_active) ? 'Aktif' : 'Non-Aktif',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Bendahara',
            'Email Akun',
            'Kelas Yang Dikelola',
            'NIS / Identitas',
            'No. Handphone',
            'Siswa Terdaftar',
            'Status Akun',
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
        return 'Data Bendahara MyCash';
    }
}
