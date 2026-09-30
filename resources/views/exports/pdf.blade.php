<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #1B4F72; padding-bottom: 12px; }
        .title { font-size: 18px; font-weight: bold; color: #1B4F72; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
        .subtitle { font-size: 11px; color: #64748B; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { border: 1px solid #CBD5E1; padding: 7px 10px; text-align: left; }
        th { background-color: #F1F5F9; color: #1B4F72; font-weight: bold; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        tr:nth-child(even) td { background-color: #F8FAFC; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #94A3B8; }
        .badge-lunas { color: #059669; font-weight: bold; }
        .badge-pending { color: #D97706; font-weight: bold; }
        .badge-overdue { color: #DC2626; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $title }}</div>
        <div class="subtitle">Kelas: {{ $kelas->nama_kelas ?? '-' }} | Dicetak pada: {{ now()->format('d M Y, H:i') }} | Sistem MyCash</div>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headings as $heading)
                    <th class="{{ in_array($heading, ['No', 'NIS']) ? 'text-center' : (str_contains($heading, '(Rp)') ? 'text-right' : '') }}">{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($row as $idx => $cell)
                        <td class="{{ $idx === 0 || $idx === 2 ? 'text-center' : (str_contains($headings[$idx] ?? '', '(Rp)') ? 'text-right' : '') }}">
                            @php
                                $headerName = strtolower($headings[$idx] ?? '');
                                $isStatusCol = in_array($headerName, ['status', 'status pembayaran', 'status akun']);
                            @endphp
                            @if($isStatusCol)
                                @if(in_array($cell, ['Up to Date', 'Lunas', 'Aktif']))
                                    <span class="badge-lunas">{{ $cell }}</span>
                                @elseif(in_array($cell, ['Pending', 'Cicil']))
                                    <span class="badge-pending">{{ $cell }}</span>
                                @else
                                    <span class="badge-overdue">{{ $cell }}</span>
                                @endif
                            @else
                                {{ $cell }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis melalui aplikasi manajemen kas kelas <strong>MyCash</strong>.
    </div>
</body>
</html>
