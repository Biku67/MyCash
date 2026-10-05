<x-app-layout>
@section('page-title', 'Checklist Kas Siswa')

<div x-data="{ 
    openSettings: false, 
    searchQuery: '',
    periodType: '{{ $feePeriodType }}',
    startMonth: '{{ $kelas->bulan_mulai ?? 'Jan' }}',
    endMonth: '{{ $kelas->bulan_selesai ?? 'Des' }}',
    feeAmount: {{ (int)$feeAmount }},
    monthKeys: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'],
    calculatePeriodCount() {
        if (this.periodType === 'mingguan') return 24;
        let s = this.monthKeys.indexOf(this.startMonth);
        let e = this.monthKeys.indexOf(this.endMonth);
        if (s === -1) s = 0;
        if (e === -1) e = 11;
        if (s <= e) return (e - s + 1);
        return (12 - s) + (e + 1);
    },
    formatRupiah(num) {
        return new Intl.NumberFormat('id-ID').format(Math.max(0, num || 0));
    }
}">
    <!-- Header Page Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-heading">Checklist Kas Siswa</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    Kelas {{ $kelas->nama_kelas ?? $kelas->kode_kelas }}
                </span>
            </div>
            <p class="text-slate-400 text-xs sm:text-sm mt-1" id="studentCount">{{ $students->count() }} siswa terdaftar dalam kelas ini</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Settings Modal Trigger -->
            <button id="tour-bendahara-btn-settings" @click="openSettings = true" class="py-2.5 px-4 text-xs sm:text-sm inline-flex items-center justify-center gap-2 bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 rounded-xl font-semibold transition-all">
                <span class="material-symbols-outlined text-base">tune</span>
                <span>Pengaturan Kas</span>
            </button>
        </div>
    </div>

    <!-- Parameter Info Card Banner -->
    <div id="tour-bendahara-students-table" class="mb-6 card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div>
                <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Ketentuan Iuran</p>
                <p class="text-sm font-bold text-slate-800 font-heading mt-0.5">
                    Periode: <span class="text-navy font-semibold">{{ $feePeriodType === 'mingguan' ? 'Mingguan (M1 - M24)' : ($kelas->bulan_mulai ?? 'Jan') . ' - ' . ($kelas->bulan_selesai ?? 'Des') . ' · ' . count($periods) . ' Bulan' }}</span> 
                    <span class="text-slate-300 mx-1.5">&bull;</span> 
                    Nominal Standar: <span class="text-emerald-600 font-bold">Rp {{ number_format($feeAmount, 0, ',', '.') }}</span>
                </p>
            </div>
        </div>
        <div class="text-xs text-slate-600 flex items-center gap-2 bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-200/60">
            <span class="material-symbols-outlined text-teal-600 text-base flex-shrink-0">sync</span>
            <span>Status kas di bawah terisi <b>otomatis</b> saat mencatat <b>Kas Masuk</b> di menu Transaksi.</span>
        </div>
    </div>

    <!-- Quick Live Search (Visible only on mobile < md) -->
    <div class="block md:hidden mb-4">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg pointer-events-none">search</span>
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Cari nama atau NIS siswa..." 
                   class="w-full pl-9 pr-3 py-2.5 text-xs bg-white border border-gray-200 rounded-xl focus:border-teal-accent focus:ring-1 focus:ring-teal-accent outline-none shadow-sm transition-all">
        </div>
    </div>

    <!-- Mobile Compact Student Cards List -->
    <div id="tour-bendahara-checklist-mobile" class="space-y-3 block md:hidden mb-6">
        @forelse($students as $index => $student)
            @php
                $totalPeriods = count($periods);
                $paidCount = count($student->paid_periods ?? []);
            @endphp
            <div class="card p-3.5 border border-gray-100 shadow-sm transition-all"
                 x-show="!searchQuery || '{{ strtolower(addslashes($student->nama)) }} {{ strtolower($student->nis ?? '') }}'.includes(searchQuery.toLowerCase())">
                <!-- Student Header -->
                <div class="flex items-center justify-between gap-2 pb-2.5 border-b border-gray-100">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-navy/10 text-navy flex items-center justify-center font-bold text-xs flex-shrink-0">
                            {{ substr($student->nama, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-gray-800 text-xs truncate leading-tight">{{ $student->nama }}</p>
                            <p class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $student->nis ?? '-' }}</p>
                        </div>
                    </div>
                    <!-- Status Chip -->
                    <div class="flex-shrink-0">
                        @if($student->outstanding_debt <= 0)
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Lunas
                            </span>
                        @elseif($student->contributed > 0)
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200/60 inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Dicicil
                            </span>
                        @else
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200/60 inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Menunggak
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Financial Summary Strip -->
                <div class="py-2 border-b border-gray-100">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <div @if($loop->first) id="tour-bendahara-col-paid-mobile" @endif>
                            <span class="text-[10px] text-gray-400 uppercase font-semibold block">Total Dibayar</span>
                            <p class="font-bold text-status-lunas text-xs total-paid-{{ $student->id }}">
                                Rp {{ number_format($student->contributed, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="text-right" @if($loop->first) id="tour-bendahara-col-debt-mobile" @endif>
                            <span class="text-[10px] text-gray-400 uppercase font-semibold block">Sisa Tagihan</span>
                            <p class="font-bold text-status-menunggak text-xs total-debt-{{ $student->id }}">
                                Rp {{ number_format($student->outstanding_debt, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <!-- Micro Progress Bar -->
                    <div class="w-full bg-slate-100 rounded-full h-1 overflow-hidden">
                        <div class="bg-gradient-to-r from-teal-accent to-emerald-500 h-1 rounded-full transition-all duration-300" style="width: {{ $totalPeriods > 0 ? round(($paidCount / $totalPeriods) * 100) : 0 }}%"></div>
                    </div>
                </div>

                <!-- Compact Period Checklist Grid (4 Columns) -->
                <div class="pt-2.5" @if($loop->first) id="tour-bendahara-col-periods-mobile" @endif>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status Periode ({{ $totalPeriods }})</span>
                        <span class="text-[10px] font-semibold text-gray-500">{{ $paidCount }}/{{ $totalPeriods }} Terbayar</span>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.375rem;">
                        @foreach($periods as $period)
                            @php
                                $isPaid = in_array($period, $student->paid_periods);
                                $partialPaid = $student->partial_periods[$period] ?? 0;
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
                <span class="material-symbols-outlined text-3xl mb-1 text-gray-300">group_off</span>
                <p class="text-xs">Belum ada data siswa di kelas ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Matrix Checklist Kas Card (Desktop View) -->
    <div id="tour-bendahara-checklist-matrix" class="card overflow-hidden hidden md:block">
        <div class="p-4 sm:p-5 overflow-x-auto w-full">
            <table id="studentsTable" class="w-full table-clean text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-navy/5 text-navy border-b border-navy/10">
                        <th class="w-8 text-center !py-2.5 !px-1 font-bold text-[11px] uppercase tracking-wider">No</th>
                        <th class="min-w-[130px] max-w-[160px] !py-2.5 !px-2 font-bold text-[11px] uppercase tracking-wider">Nama Siswa</th>
                        <th class="min-w-[70px] !py-2.5 !px-1.5 font-bold text-[11px] uppercase tracking-wider">NIS</th>
                        @foreach($periods as $period)
                            <th @if($loop->first) id="tour-bendahara-col-periods" @endif class="text-center min-w-[36px] lg:min-w-[42px] !px-0.5 !py-2 font-bold text-[10px] uppercase tracking-wider">
                                <span class="block leading-tight text-slate-700 font-extrabold">{{ $period }}</span>
                            </th>
                        @endforeach
                        <th id="tour-bendahara-col-paid" class="min-w-[95px] !py-2.5 !px-2 text-right font-bold text-[11px] uppercase tracking-wider">Total Dibayar</th>
                        <th id="tour-bendahara-col-debt" class="min-w-[95px] !py-2.5 !px-2 text-right font-bold text-[11px] uppercase tracking-wider">Belum Dibayar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        <tr class="hover:bg-navy/5 transition-colors duration-150 border-b border-slate-100">
                            <td class="text-center font-medium text-gray-500 !py-2 !px-1 text-xs">{{ $index + 1 }}</td>
                            <td class="!py-2 !px-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-navy/10 text-navy flex items-center justify-center font-bold text-xs flex-shrink-0">
                                        {{ substr($student->nama, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 leading-tight text-xs truncate max-w-[130px]">{{ $student->nama }}</p>
                                        <p class="text-[10px] text-gray-400 mt-0.5 truncate max-w-[130px]">{{ $student->user->email ?? $student->no_hp ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="font-mono text-xs text-gray-600 !py-2 !px-1.5">{{ $student->nis ?? '-' }}</td>
                            @foreach($periods as $period)
                                @php
                                    $isPaid = in_array($period, $student->paid_periods);
                                    $partialPaid = $student->partial_periods[$period] ?? 0;
                                @endphp
                                <td class="text-center align-middle !px-0.5 !py-1.5 transition-colors duration-150 {{ $isPaid ? 'bg-emerald-50/50' : ($partialPaid > 0 ? 'bg-amber-50/60' : 'bg-transparent') }}">
                                    <div class="inline-flex flex-col items-center justify-center">
                                        @if($isPaid)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-emerald-100/90 text-emerald-700 font-bold text-xs shadow-2xs" 
                                                  title="{{ $student->nama }} - {{ $period }}: Lunas">✓</span>
                                        @elseif($partialPaid > 0)
                                            <span class="partial-badge inline-flex items-center justify-center px-1 h-5 rounded-md bg-amber-100 text-amber-800 font-bold text-[9px]" 
                                                  title="{{ $student->nama }} - {{ $period }}: Dicicil Rp {{ number_format($partialPaid, 0, ',', '.') }}">
                                                {{ $partialPaid >= 1000 ? round($partialPaid/1000, 1) . 'k' : $partialPaid }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-md text-gray-300 font-bold text-xs select-none" 
                                                  title="{{ $student->nama }} - {{ $period }}: Belum Bayar">-</span>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                            <td class="font-semibold text-status-lunas text-xs !py-2 !px-2 text-right whitespace-nowrap total-paid-{{ $student->id }}" id="total-paid-{{ $student->id }}">
                                Rp {{ number_format($student->contributed, 0, ',', '.') }}
                            </td>
                            <td class="font-semibold text-status-menunggak text-xs !py-2 !px-2 text-right whitespace-nowrap total-debt-{{ $student->id }}" id="total-debt-{{ $student->id }}">
                                Rp {{ number_format($student->outstanding_debt, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($periods) + 4 }}" class="text-center py-10 text-gray-400">
                                <span class="material-symbols-outlined text-3xl mb-1 text-gray-300">group_off</span>
                                <p class="text-sm">Belum ada data siswa di kelas ini.</p>
                                <p class="text-xs text-gray-400 mt-1">Data siswa dikelola oleh Wali Kelas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Pengaturan Parameter Kas -->
    <div x-show="openSettings" 
         x-cloak
         style="display: none;"
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop Overlay -->
        <div x-show="openSettings"
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="openSettings = false"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

        <!-- Centering Wrapper -->
        <div class="fixed inset-0 z-10 flex min-h-full items-center justify-center p-4">
            <!-- Modal Dialog Box -->
            <div x-show="openSettings"
                 x-transition:enter="transition-all ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition-all ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-md p-6 border border-gray-100"
                 style="max-width: 480px; width: 100%;"
                 @click.stop>
                
                <div class="flex items-center justify-between mb-5 border-b border-gray-100 pb-3.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-teal-accent/15 text-teal-accent flex items-center justify-center">
                            <span class="material-symbols-outlined text-xl">tune</span>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-navy" style="font-family:'Manrope',sans-serif;">Pengaturan Iuran Kas</h3>
                            <p class="text-[11px] text-gray-400">Atur periode dan nominal iuran kas</p>
                        </div>
                    </div>
                    <button type="button" @click="openSettings = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('bendahara.students.updateFeeSettings') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Tipe Periode Pembayaran</label>
                        <select name="fee_period_type" x-model="periodType" class="ui selection dropdown input-clean w-full text-sm">
                            <option value="bulanan">Bulanan</option>
                            <option value="mingguan">Mingguan (M1 - M24)</option>
                        </select>
                        <p class="text-[11px] text-gray-400 mt-1">Mengubah tipe periode akan menyesuaikan jumlah kolom checklist matriks.</p>
                    </div>

                    <!-- Custom Month Range Selector (Only visible if periodType === 'bulanan') -->
                    <div x-show="periodType === 'bulanan'" x-transition class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-sm text-teal-accent">calendar_month</span>
                                Rentang Bulan Aktif
                            </span>
                            <span class="text-[11px] font-bold text-teal-700 bg-teal-50 border border-teal-200 px-2 py-0.5 rounded-full" x-text="calculatePeriodCount() + ' Bulan'"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Bulan Mulai</label>
                                <select name="start_month" x-model="startMonth" class="ui selection dropdown input-clean w-full text-xs font-semibold">
                                    @foreach($monthList as $kMonth => $labelMonth)
                                        <option value="{{ $kMonth }}">{{ $kMonth }} ({{ $labelMonth }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Bulan Selesai</label>
                                <select name="end_month" x-model="endMonth" class="ui selection dropdown input-clean w-full text-xs font-semibold">
                                    @foreach($monthList as $kMonth => $labelMonth)
                                        <option value="{{ $kMonth }}">{{ $kMonth }} ({{ $labelMonth }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 leading-relaxed">
                            Mendukung rentang dalam tahun yang sama (cth: Jul - Des = 6 bulan) maupun lintas tahun (cth: Jul - Jun = 12 bulan).
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Nominal Standar per Periode (Rp)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-xs font-bold">Rp</span>
                            <input type="number" name="fee_amount" x-model.number="feeAmount" required min="1000" step="500" class="input-clean w-full pl-9 pr-3 py-2.5 text-sm font-semibold text-navy" placeholder="20000">
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1">Nominal yang otomatis dicatat saat kotak periode dicentang.</p>
                    </div>

                    <!-- Live Calculation Summary Card -->
                    <div class="bg-blue-50/70 border border-blue-100 rounded-xl p-3 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600 text-lg">calculate</span>
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-500">Estimasi Target Kas / Siswa</p>
                                <p class="font-bold text-blue-900 font-heading">
                                    <span x-text="calculatePeriodCount()"></span> periode &times; Rp <span x-text="formatRupiah(feeAmount)"></span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-extrabold text-blue-700">
                                Rp <span x-text="formatRupiah(calculatePeriodCount() * feeAmount)"></span>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-gray-100 mt-6">
                        <button type="button" @click="openSettings = false" class="px-4 py-2 border border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">Batal</button>
                        <button type="submit" class="px-5 py-2 btn-navy text-xs font-bold shadow-sm">Simpan Pengaturan</button>
                    </div>
                </form>

                <!-- Section Separate: Reset Matriks Checklist -->
                <div class="mt-6 pt-4 border-t border-rose-100 bg-rose-50/50 p-3.5 rounded-xl border">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="text-xs font-bold text-rose-800 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-rose-600">restart_alt</span>
                                Mulai Periode Baru (Reset Matriks)
                            </span>
                            <p class="text-[11px] text-rose-600/90 mt-0.5 leading-relaxed">
                                Kosongkan centang checklist matriks kas siswa. Catatan transaksi dan riwayat laporan keuangan tetap tersimpan utuh.
                            </p>
                        </div>
                        <form method="POST" action="{{ route('bendahara.students.resetMatrix') }}" onsubmit="return swalConfirm(event, 'Reset Matriks Checklist Kas?', 'Apakah Anda yakin ingin me-reset centang checklist kas untuk memulai periode baru? Catatan transaksi dan riwayat laporan keuangan tetap tersimpan utuh.')" class="flex-shrink-0">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold shadow-sm transition-colors flex items-center justify-center gap-1.5">
                                <span class="material-symbols-outlined text-sm">restart_alt</span>
                                <span>Reset Matriks</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    if ($('#studentsTable tbody tr').length > 1 || !$('#studentsTable tbody td[colspan]').length) {
        // Compute period column indices to disable sorting
        const totalCols = $('#studentsTable thead th').length;
        const nonSortableTargets = [0]; // No column
        for (let i = 3; i < totalCols; i++) {
            nonSortableTargets.push(i);
        }

        $('#studentsTable').DataTable({
            pageLength: 15,
            ordering: true,
            autoWidth: false,
            columnDefs: [
                { orderable: false, targets: nonSortableTargets }
            ],
            dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4 gap-3"lf>rt<"flex flex-col sm:flex-row justify-between items-center mt-4 gap-3"ip>',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari siswa atau NIS...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ siswa",
                infoEmpty: "Tidak ada data",
                zeroRecords: "Tidak ada siswa yang sesuai",
                paginate: {
                    previous: "←",
                    next: "→"
                }
            }
        });
    }
});
</script>
</x-app-layout>
