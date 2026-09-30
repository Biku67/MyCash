<?php

namespace App\Exports;

use App\Models\TransaksiKas;
use App\Models\Kelas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;
use Illuminate\Support\Enumerable;

class BendaharaReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    protected $kodeKelas;
    protected $startDate;
    protected $endDate;
    protected $categoryId;
    protected $type;
    protected $kelas;
    protected $totalRowsCount = 0;

    public function __construct($kodeKelas, $startDate = null, $endDate = null, $categoryId = null, $type = 'all')
    {
        $this->kodeKelas = $kodeKelas;
        $this->startDate = $startDate ?: now()->startOfMonth()->format('Y-m-d');
        $this->endDate = $endDate ?: now()->endOfMonth()->format('Y-m-d');
        $this->categoryId = !empty($categoryId) ? $categoryId : null;
        $this->type = (!empty($type) && $type !== 'all') ? $type : 'all';
        $this->kelas = Kelas::where('kode_kelas', $kodeKelas)->first();
    }

    public function collection(): Enumerable
    {
        $baseQuery = TransaksiKas::where('kode_kelas', $this->kodeKelas);

        if ($this->categoryId) {
            $baseQuery->where('id_kategori', $this->categoryId);
        }

        if ($this->type !== 'all') {
            $baseQuery->where('jenis_transaksi', $this->type);
        }

        // Calculate Saldo Awal before startDate
        $incomeBefore = (clone $baseQuery)
            ->whereDate('tanggal_transaksi', '<', $this->startDate)
            ->where('jenis_transaksi', 'pemasukan')
            ->sum('total_nominal');

        $expenseBefore = (clone $baseQuery)
            ->whereDate('tanggal_transaksi', '<', $this->startDate)
            ->where('jenis_transaksi', 'pengeluaran')
            ->sum('total_nominal');

        $running = (float)($incomeBefore - $expenseBefore);

        $query = (clone $baseQuery)
            ->with(['kategori', 'bendahara', 'detailTransaksi.siswa'])
            ->whereBetween('tanggal_transaksi', [$this->startDate, $this->endDate]);

        $transactions = $query->orderBy('tanggal_transaksi', 'asc')->orderBy('id', 'asc')->get();

        $rows = collect();

        // 1. Opening Balance row
        $rows->push([
            'no' => '-',
            'tanggal' => Carbon::parse($this->startDate)->format('d/m/Y'),
            'jenis' => 'Saldo Awal',
            'kategori' => 'Akumulasi Kas Sebelumnya',
            'keterangan' => 'Saldo kas awal sebelum periode ' . Carbon::parse($this->startDate)->format('d/m/Y'),
            'pemasukan' => $running >= 0 ? $running : 0,
            'pengeluaran' => $running < 0 ? abs($running) : 0,
            'saldo' => $running,
            'dicatat_oleh' => 'Sistem',
        ]);

        $no = 1;
        $totalMasuk = 0;
        $totalKeluar = 0;

        foreach ($transactions as $tx) {
            $nominal = (float)$tx->total_nominal;
            $masuk = $tx->jenis_transaksi === 'pemasukan' ? $nominal : 0;
            $keluar = $tx->jenis_transaksi === 'pengeluaran' ? $nominal : 0;
            $totalMasuk += $masuk;
            $totalKeluar += $keluar;
            $running += ($masuk - $keluar);

            $desc = $tx->keterangan ?? '-';
            $studentName = $tx->detailTransaksi->first()?->siswa?->nama;
            if ($studentName) {
                $desc .= ' (Siswa: ' . $studentName . ')';
            }

            $rows->push([
                'no' => $no++,
                'tanggal' => Carbon::parse($tx->tanggal_transaksi)->format('d/m/Y'),
                'jenis' => ucfirst($tx->jenis_transaksi),
                'kategori' => $tx->kategori->nama_kategori ?? '-',
                'keterangan' => $desc,
                'pemasukan' => $masuk,
                'pengeluaran' => $keluar,
                'saldo' => $running,
                'dicatat_oleh' => $tx->bendahara->nama ?? 'Bendahara',
            ]);
        }

        // Summary row at the bottom
        $rows->push([
            'no' => '',
            'tanggal' => '',
            'jenis' => 'TOTAL MUTASI',
            'kategori' => 'Total Periode ' . Carbon::parse($this->startDate)->format('d/m/Y') . ' - ' . Carbon::parse($this->endDate)->format('d/m/Y'),
            'keterangan' => 'Saldo Kas Akhir Periode',
            'pemasukan' => $totalMasuk,
            'pengeluaran' => $totalKeluar,
            'saldo' => $running,
            'dicatat_oleh' => '',
        ]);

        $this->totalRowsCount = $rows->count() + 1; // +1 for heading row

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Jenis Transaksi',
            'Kategori',
            'Keterangan',
            'Pemasukan (Rp)',
            'Pengeluaran (Rp)',
            'Saldo Berjalan (Rp)',
            'Dicatat Oleh',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => '#,##0',
            'G' => '#,##0',
            'H' => '#,##0',
        ];
    }

    public function styles(Worksheet $sheet): ?array
    {
        $lastRow = $this->totalRowsCount > 1 ? $this->totalRowsCount : 3;

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1B4F72'],
                ],
            ],
            2 => [
                'font' => ['italic' => true, 'bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F1F5F9'],
                ],
            ],
            $lastRow => [
                'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Kas ' . $this->kodeKelas;
    }
}
