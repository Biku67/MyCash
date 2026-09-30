<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kas Kelas {{ $kelas->nama_kelas ?? '' }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.4;
            margin: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1B4F72;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            color: #1B4F72;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 11px;
            color: #475569;
            font-weight: 500;
        }
        .meta-container {
            width: 100%;
            margin-bottom: 16px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 6px;
            font-size: 10px;
            border: none;
        }
        /* Summary Grid */
        .summary-box {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: collapse;
        }
        .summary-box td {
            border: 1px solid #CBD5E1;
            padding: 8px 10px;
            background: #F8FAFC;
        }
        .summary-title {
            font-size: 8.5px;
            text-transform: uppercase;
            color: #64748B;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .summary-amount {
            font-size: 13px;
            font-weight: bold;
            color: #0F172A;
            margin-top: 3px;
        }
        .text-green { color: #059669; }
        .text-red { color: #DC2626; }
        .text-navy { color: #1B4F72; }

        /* Data Table */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            page-break-inside: auto;
        }
        table.data-table thead {
            display: table-header-group;
        }
        table.data-table tfoot {
            display: table-footer-group;
        }
        table.data-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #CBD5E1;
            padding: 6px 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #1B4F72;
            color: #ffffff;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #F8FAFC;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        /* Signatures */
        .signatures {
            margin-top: 35px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signatures table {
            width: 100%;
            border: none;
        }
        .signatures td {
            border: none;
            width: 50%;
            text-align: center;
            font-size: 10px;
            vertical-align: top;
        }
        .signature-line {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
            color: #0F172A;
        }
        .signature-title {
            color: #64748B;
            font-size: 9px;
            margin-top: 2px;
        }
        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 8.5px;
            color: #94A3B8;
            border-top: 1px solid #E2E8F0;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    @php
        // Safely resolve Bendahara model/name
        $resolvedBendahara = null;
        if ($bendahara instanceof \App\Models\Bendahara) {
            $resolvedBendahara = $bendahara;
        } elseif ($bendahara instanceof \Illuminate\Support\Collection) {
            $resolvedBendahara = $bendahara->first();
        } elseif (isset($kelas) && $kelas->relationLoaded('bendahara')) {
            $resolvedBendahara = $kelas->bendahara->first();
        } elseif (isset($kelas)) {
            $resolvedBendahara = $kelas->bendahara()->first();
        }
        $bendaharaNama = $resolvedBendahara->nama ?? 'Bendahara Kelas';

        // Safely resolve Wali Kelas model/name
        $resolvedWaliKelas = null;
        if ($waliKelas instanceof \App\Models\WaliKelas) {
            $resolvedWaliKelas = $waliKelas;
        } elseif ($waliKelas instanceof \Illuminate\Support\Collection) {
            $resolvedWaliKelas = $waliKelas->first();
        } elseif (isset($kelas) && $kelas->relationLoaded('waliKelas')) {
            $resolvedWaliKelas = $kelas->waliKelas;
        } elseif (isset($kelas)) {
            $resolvedWaliKelas = $kelas->waliKelas()->first();
        }
        $waliKelasNama = $resolvedWaliKelas->nama ?? '(..........................................)';
        $waliKelasNip = $resolvedWaliKelas->nip ?? '-';
    @endphp

    <div class="header">
        <div class="title">Laporan Pertanggungjawaban Kas Kelas</div>
        <div class="subtitle">Kelas {{ $kelas->nama_kelas ?? '-' }} &bull; Sistem Pengelolaan Kas MyCash</div>
    </div>

    <table class="meta-table meta-container">
        <tr>
            <td style="width: 18%; font-weight: bold; color: #475569;">Periode Laporan</td>
            <td style="width: 32%;">: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</td>
            <td style="width: 18%; font-weight: bold; color: #475569;">Wali Kelas</td>
            <td style="width: 32%;">: {{ $waliKelasNama }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #475569;">Waktu Cetak</td>
            <td>: {{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
            <td style="font-weight: bold; color: #475569;">Bendahara</td>
            <td>: {{ $bendaharaNama }}</td>
        </tr>
    </table>

    <!-- Ringkasan Saldo -->
    <table class="summary-box">
        <tr>
            <td>
                <div class="summary-title">Saldo Awal Periode</div>
                <div class="summary-amount text-navy">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="summary-title">Total Pemasukan</div>
                <div class="summary-amount text-green">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="summary-title">Total Pengeluaran</div>
                <div class="summary-amount text-red">-Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="summary-title">Saldo Kas Akhir</div>
                <div class="summary-amount text-navy font-bold">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <!-- Tabel Rincian Mutasi Transaksi -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th style="width: 65px;">Tanggal</th>
                <th style="width: 90px;">Kategori</th>
                <th>Keterangan / Rincian</th>
                <th class="text-right" style="width: 75px;">Masuk (Rp)</th>
                <th class="text-right" style="width: 75px;">Keluar (Rp)</th>
                <th class="text-right" style="width: 80px;">Saldo (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="color: #64748B;">-</td>
                <td style="color: #64748B;">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</td>
                <td style="color: #64748B; font-weight: bold;">SALDO AWAL</td>
                <td style="color: #64748B; font-style: italic;">Akumulasi kas sebelum periode laporan</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right font-bold" style="color: #1B4F72;">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
            </tr>

            @forelse($transactions as $index => $tx)
                @php
                    $isIncome = $tx->jenis_transaksi === 'pemasukan';
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($tx->tanggal_transaksi)->format('d/m/Y') }}</td>
                    <td>{{ $tx->kategori->nama_kategori ?? '-' }}</td>
                    <td>
                        {{ $tx->keterangan ?? '-' }}
                        @if($studentName = $tx->detailTransaksi->first()?->siswa?->nama)
                            <br><span style="font-size: 8.5px; color: #64748B;">(Siswa: {{ $studentName }})</span>
                        @endif
                    </td>
                    <td class="text-right {{ $isIncome ? 'text-green font-bold' : '' }}">
                        {{ $isIncome ? number_format($tx->total_nominal, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right {{ !$isIncome ? 'text-red font-bold' : '' }}">
                        {{ !$isIncome ? number_format($tx->total_nominal, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right font-bold text-navy">
                        Rp {{ number_format($tx->running_balance, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #94A3B8;">
                        Tidak ada catatan transaksi kas pada rentang tanggal ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #F1F5F9; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAL MUTASI PERIODE INI :</td>
                <td class="text-right text-green">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                <td class="text-right text-red">-Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                <td class="text-right text-navy font-bold">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Tanda Tangan -->
    <div class="signatures">
        <table>
            <tr>
                <td>
                    <p>Mengetahui,</p>
                    <p>Wali Kelas {{ $kelas->nama_kelas ?? '' }}</p>
                    <div class="signature-line">{{ $waliKelasNama }}</div>
                    <div class="signature-title">NIP / Identitas: {{ $waliKelasNip }}</div>
                </td>
                <td>
                    <p>Dicatat & Dilaporkan Oleh,</p>
                    <p>Bendahara Kelas</p>
                    <div class="signature-line">{{ $bendaharaNama }}</div>
                    <div class="signature-title">Bendahara Kelas {{ $kelas->nama_kelas ?? '' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dicetak secara otomatis melalui Sistem Informasi Kas Kelas MyCash pada {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
