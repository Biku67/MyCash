<?php

namespace App\Exports;

use App\Models\TransaksiKas;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use Illuminate\Support\Enumerable;

class BendaharaTransactionExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $kodeKelas;
    protected $kelas;

    public function __construct($kodeKelas)
    {
        $this->kodeKelas = $kodeKelas;
        $this->kelas = Kelas::where('kode_kelas', $kodeKelas)->first();
    }

    public function collection(): Enumerable
    {
        $transactions = TransaksiKas::where('kode_kelas', $this->kodeKelas)
            ->with(['kategori', 'bendahara'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $no = 1;
        return $transactions->map(function ($tx) use (&$no) {
            return [
                'no' => $no++,
                'tanggal' => Carbon::parse($tx->tanggal_transaksi)->format('d/m/Y'),
                'jenis' => ucfirst($tx->jenis_transaksi),
                'kategori' => $tx->kategori->nama_kategori ?? '-',
                'keterangan' => $tx->keterangan,
                'nominal' => (float)$tx->total_nominal,
                'dicatat_oleh' => $tx->bendahara->nama ?? 'Bendahara',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Jenis Transaksi',
            'Kategori',
            'Keterangan',
            'Nominal (Rp)',
            'Dicatat Oleh',
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
        return 'Transaksi Kas ' . ($this->kelas->nama_kelas ?? '');
    }
}
