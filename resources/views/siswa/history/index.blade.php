<x-app-layout>
@section('page-title', 'Riwayat Pembayaran Kas')

@if(!$student)
    <div class="card p-6 border border-gray-100 text-center max-w-xl mx-auto my-8 shadow-sm">
        <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-3xl">warning</span>
        </div>
        <h2 class="text-xl font-bold text-navy" style="font-family:'Manrope',sans-serif;">Akun Belum Ditautkan</h2>
        <p class="text-sm text-gray-500 mt-2 leading-relaxed">
            Akun ini belum terhubung dengan data siswa di kelas. Hubungi wali kelas atau admin sekolah untuk menyambungkan akunmu.
        </p>
    </div>
@else
<div>
    <!-- Header Page Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-bold text-navy" style="font-family:'Manrope',sans-serif;">Riwayat Pembayaran Kas</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-navy/10 text-navy border border-navy/20">
                    Kelas {{ $kelas->nama_kelas ?? $kelas->kode_kelas }}
                </span>
            </div>
            <p class="text-gray-500 text-sm mt-1">Cek riwayat setoran uang kas yang telah dicatat bendahara kelas.</p>
        </div>
    </div>

    <!-- Parameter Info Card Banner -->
    <div id="tour-siswa-history-matrix" class="mb-6 card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div>
                <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Ketentuan Iuran</p>
                <p class="text-sm font-bold text-slate-800 font-heading mt-0.5">
                    Periode: <span class="text-navy font-semibold">{{ $feePeriodType === 'mingguan' ? 'Mingguan (M1 - M24)' : 'Bulanan (' . ($kelas->bulan_mulai ?? 'Jan') . ' - ' . ($kelas->bulan_selesai ?? 'Des') . ' · ' . count($periods) . ' Bulan)' }}</span> 
                    <span class="text-slate-300 mx-1.5">&bull;</span> 
                    Nominal Standar: <span class="text-emerald-600 font-bold">Rp {{ number_format($feeAmount, 0, ',', '.') }}</span>
                </p>
            </div>
        </div>
        <div class="text-xs text-slate-600 flex items-center gap-2 bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-200/60">
            <span class="material-symbols-outlined text-teal-600 text-base flex-shrink-0">sync</span>
            <span>Status kas di bawah terisi <b>otomatis</b> setiap bendahara mencatat setoran Anda.</span>
        </div>
    </div>

    <!-- Matrix Checklist Kas Card (Desktop View) -->
    <div class="card overflow-hidden hidden md:block">
        <div class="p-5 overflow-x-auto w-full">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-700 text-lg">table_chart</span>
                    <h3 class="font-bold text-slate-900 text-base font-heading">Matriks Pembayaran Kas Siswa</h3>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 inline-flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>{{ count($periods) }} Periode
                    </span>
                </div>
            </div>

            <table id="studentsTable" class="w-full table-clean text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-navy/5 text-navy border-b border-navy/10">
                        <th class="w-8 text-center !py-2.5 !px-1 font-bold text-[11px] uppercase tracking-wider">No</th>
                        <th class="min-w-[130px] max-w-[160px] !py-2.5 !px-2.5 font-bold text-[11px] uppercase tracking-wider">Nama Siswa</th>
                        <th class="min-w-[70px] !py-2.5 !px-1.5 font-bold text-[11px] uppercase tracking-wider">NIS</th>
                        @foreach($periods as $period)
                            <th class="text-center min-w-[36px] lg:min-w-[42px] !px-0.5 !py-2 font-bold text-[10px] uppercase tracking-wider">
                                <span class="block leading-tight text-slate-700 font-extrabold">{{ $period }}</span>
                            </th>
                        @endforeach
                        <th class="min-w-[95px] !py-2.5 !px-2.5 font-bold text-[11px] uppercase tracking-wider text-right">Total Dibayar</th>
                        <th class="min-w-[95px] !py-2.5 !px-2.5 font-bold text-[11px] uppercase tracking-wider text-right">Belum Dibayar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $item)
                        <tr class="hover:bg-navy/5 transition-colors duration-150 border-b border-slate-100">
                            <td class="text-center font-bold text-gray-500 !py-2 !px-1 text-xs">{{ $index + 1 }}</td>
                            <td class="!py-2 !px-2.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-navy/10 text-navy flex items-center justify-center font-bold text-xs shadow-xs flex-shrink-0">
                                        {{ substr($item->nama, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-900 text-xs leading-tight truncate max-w-[130px]">{{ $item->nama }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5 truncate max-w-[130px]">{{ $item->user->email ?? $item->no_hp ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="!py-2 !px-1.5 font-mono text-xs font-semibold text-gray-600">{{ $item->nis ?? '-' }}</td>
                            @foreach($periods as $period)
                                @php
                                    $isPaid = in_array($period, $item->paid_periods);
                                    $partialPaid = $item->partial_periods[$period] ?? 0;
                                @endphp
                                <td class="text-center align-middle !px-0.5 !py-1.5 transition-colors duration-150 {{ $isPaid ? 'bg-emerald-50/50' : ($partialPaid > 0 ? 'bg-amber-50/60' : 'bg-transparent') }}">
                                    <div class="inline-flex flex-col items-center justify-center">
                                        @if($isPaid)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-emerald-100/90 text-emerald-700 font-bold text-xs shadow-2xs" 
                                                  title="{{ $item->nama }} - {{ $period }}: Lunas">✓</span>
                                        @elseif($partialPaid > 0)
                                            <span class="partial-badge inline-flex items-center justify-center px-1 h-5 rounded-md bg-amber-100 text-amber-800 font-bold text-[9px]" 
                                                  title="{{ $item->nama }} - {{ $period }}: Dicicil Rp {{ number_format($partialPaid, 0, ',', '.') }}">
                                                {{ $partialPaid >= 1000 ? round($partialPaid/1000, 1) . 'k' : $partialPaid }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-md text-gray-300 font-bold text-xs select-none" 
                                                  title="{{ $item->nama }} - {{ $period }}: Belum Bayar">-</span>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                            <td class="!py-2 !px-2.5 font-bold text-xs text-emerald-600 text-right whitespace-nowrap">
                                Rp {{ number_format($item->contributed, 0, ',', '.') }}
                            </td>
                            <td class="!py-2 !px-2.5 font-bold text-xs text-amber-600 text-right whitespace-nowrap">
                                Rp {{ number_format($item->outstanding_debt, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($periods) + 4 }}" class="text-center py-10 text-gray-400">
                                <span class="material-symbols-outlined text-3xl mb-1 text-gray-300">person_off</span>
                                <p class="text-sm">Data siswa Anda tidak ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile View (Visible on mobile screens < md: compact card layout) -->
    <div class="space-y-3 block md:hidden mb-6">
        @forelse($students as $index => $item)
            @php
                $totalPeriods = count($periods);
                $paidCount = count($item->paid_periods ?? []);
            @endphp
            <div class="card p-3.5 border border-gray-100 shadow-sm transition-all">
                <!-- Student Header -->
                <div class="flex items-center justify-between gap-2 pb-2.5 border-b border-gray-100">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-navy/10 text-navy flex items-center justify-center font-bold text-xs flex-shrink-0">
                            {{ substr($item->nama, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-gray-800 text-xs truncate leading-tight">{{ $item->nama }}</p>
                            <p class="text-[10px] text-gray-400 font-mono mt-0.5">NIS: {{ $item->nis ?? '-' }}</p>
                        </div>
                    </div>
                    <!-- Status Chip -->
                    <div class="flex-shrink-0">
                        @if($item->outstanding_debt <= 0)
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Lunas
                            </span>
                        @else
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200/60 inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Belum Lunas
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Financial Summary Strip -->
                <div class="py-2 border-b border-gray-100">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <div>
                            <span class="text-[10px] text-gray-400 uppercase font-semibold block">Total Dibayar</span>
                            <p class="font-bold text-status-lunas text-xs">
                                Rp {{ number_format($item->contributed, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-gray-400 uppercase font-semibold block">Sisa Tagihan</span>
                            <p class="font-bold text-status-menunggak text-xs">
                                Rp {{ number_format($item->outstanding_debt, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <!-- Micro Progress Bar -->
                    <div class="w-full bg-slate-100 rounded-full h-1 overflow-hidden">
                        <div class="bg-gradient-to-r from-teal-accent to-emerald-500 h-1 rounded-full transition-all duration-300" style="width: {{ $totalPeriods > 0 ? round(($paidCount / $totalPeriods) * 100) : 0 }}%"></div>
                    </div>
                </div>

                <!-- Compact Period Status Grid (4 Columns) -->
                <div class="pt-2.5">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status Periode ({{ $totalPeriods }})</span>
                        <span class="text-[10px] font-semibold text-gray-500">{{ $paidCount }}/{{ $totalPeriods }} Terbayar</span>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.375rem;">
                        @foreach($periods as $period)
                            @php
                                $isPaid = in_array($period, $item->paid_periods);
                                $partialPaid = $item->partial_periods[$period] ?? 0;
                            @endphp
                            <div class="flex items-center justify-between px-2 py-1.5 rounded-lg border select-none {{ $isPaid ? 'bg-emerald-50/90 text-emerald-800 border-emerald-300/80 shadow-xs' : ($partialPaid > 0 ? 'bg-amber-50/90 text-amber-800 border-amber-300/80' : 'bg-gray-50/80 text-gray-400 border-gray-200/60') }}"
                                 title="{{ $period }}: {{ $isPaid ? 'Lunas' : ($partialPaid > 0 ? 'Dicicil Rp ' . number_format($partialPaid, 0, ',', '.') : 'Belum Bayar') }}">
                                <div class="min-w-0 pr-1">
                                    <span class="text-[10px] font-bold block leading-none truncate {{ $isPaid ? 'text-emerald-900' : ($partialPaid > 0 ? 'text-amber-900' : 'text-gray-500') }}">{{ $period }}</span>
                                    @if(!$isPaid && $partialPaid > 0)
                                        <span class="partial-badge text-[7px] font-semibold text-amber-700 block leading-none truncate mt-0.5">
                                            {{ $partialPaid >= 1000 ? round($partialPaid/1000, 1) . 'k' : $partialPaid }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex-shrink-0">
                                    @if($isPaid)
                                        <span class="w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold leading-none">✓</span>
                                    @elseif($partialPaid > 0)
                                        <span class="w-4 h-4 rounded-full bg-amber-500 text-white flex items-center justify-center text-[8px] font-bold leading-none">½</span>
                                    @else
                                        <span class="w-4 h-4 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-[10px] font-bold leading-none">-</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-6 text-center text-gray-400">
                <span class="material-symbols-outlined text-3xl mb-1 text-gray-300">person_off</span>
                <p class="text-xs">Data siswa Anda tidak ditemukan.</p>
            </div>
        @endforelse
    </div>

    <!-- Catatan Transparansi -->
    <div class="mt-6 card p-5 flex items-start gap-3.5">
        <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-lg">info</span>
        </div>
        <div class="text-xs text-slate-600 leading-relaxed">
            <span class="font-bold text-slate-800 font-heading">Transparansi Data Kas:</span>
            Status pembayaran di atas bersumber langsung dari pencatatan bendahara kelas. Apabila terdapat setoran yang belum tercatat atau membutuhkan penyesuaian, silakan hubungi bendahara kelas Anda.
        </div>
    </div>
</div>
@endif
</x-app-layout>
