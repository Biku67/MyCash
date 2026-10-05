<x-app-layout>
@section('page-title', 'Checklist Kas Siswa')

<div class="space-y-6">
    <!-- Header Page Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-heading">Checklist Kas Siswa</h1>
                @if($kelas)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        Kelas {{ $kelas->nama_kelas ?? $kelas->kode_kelas }}
                    </span>
                @endif
            </div>
            @if($kelas)
                <p class="text-slate-400 text-xs sm:text-sm mt-1" id="studentCount">{{ $students->count() }} siswa terdaftar dalam kelas ini</p>
            @endif
        </div>
    </div>

    @if(!$kelas)
        <div class="card p-12 text-center text-slate-400 border border-slate-200/80 rounded-2xl shadow-sm">
            <span class="material-symbols-outlined text-5xl mb-2 text-slate-300">school</span>
            <h3 class="text-base font-bold text-slate-700">Belum Ada Kelas</h3>
            <p class="text-xs text-slate-500 mt-1">Anda belum ditugaskan sebagai wali kelas. Silakan hubungi admin sekolah.</p>
        </div>
    @else
        <!-- Multi-Class Selector Pills (if wali kelas manages > 1 class) -->
        @if(isset($kelasList) && $kelasList->count() > 1)
            <div class="flex items-center gap-2 overflow-x-auto pb-1">
                <span class="text-xs text-slate-500 font-semibold whitespace-nowrap">Pilih Kelas:</span>
                @foreach($kelasList as $k)
                    <a href="{{ route('wali-kelas.checklist.index', ['kode_kelas' => $k->kode_kelas]) }}" 
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $selectedKodeKelas === $k->kode_kelas ? 'bg-navy text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $k->nama_kelas ?? $k->kode_kelas }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Parameter Info Card Banner -->
        <div id="tour-wali-checklist-banner" class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Ketentuan Iuran Kelas</p>
                    <p class="text-sm font-bold text-slate-800 font-heading mt-0.5">
                        Periode: <span class="text-navy font-semibold">{{ $feePeriodType === 'mingguan' ? 'Mingguan (M1 - M24)' : ($kelas->bulan_mulai ?? 'Jan') . ' - ' . ($kelas->bulan_selesai ?? 'Des') . ' · ' . count($periods) . ' Bulan' }}</span> 
                        <span class="text-slate-300 mx-1.5">&bull;</span> 
                        Nominal Standar: <span class="text-emerald-600 font-bold">Rp {{ number_format($feeAmount, 0, ',', '.') }}</span>
                    </p>
                </div>
            </div>
            <div class="text-xs text-slate-600 flex items-center gap-2 bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-200/60">
                <span class="material-symbols-outlined text-teal-600 text-base flex-shrink-0">sync</span>
                <span>Status pembayaran terisi <b>otomatis</b> berdasarkan catatan kas dari bendahara kelas.</span>
            </div>
        </div>

        <!-- Quick Live Search (Visible only on mobile < md) -->
        <div class="block md:hidden mb-4" x-data="{ searchQuery: '' }">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg pointer-events-none">search</span>
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Cari nama atau NIS siswa..." 
                       class="w-full pl-9 pr-3 py-2.5 text-xs bg-white border border-gray-200 rounded-xl focus:border-teal-accent focus:ring-1 focus:ring-teal-accent outline-none shadow-sm transition-all">
            </div>
        </div>

        <!-- Mobile Compact Student Cards List -->
        <div id="tour-wali-checklist-mobile" class="space-y-3 block md:hidden mb-6">
            @forelse($students as $index => $student)
                @php
                    $totalPeriods = count($periods);
                    $paidCount = count($student->paid_periods ?? []);
                @endphp
                <div class="card p-3.5 border border-gray-100 shadow-sm transition-all">
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
                            <div>
                                <span class="text-[10px] text-gray-400 uppercase font-semibold block">Total Dibayar</span>
                                <p class="font-bold text-status-lunas text-xs">
                                    Rp {{ number_format($student->contributed, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-gray-400 uppercase font-semibold block">Sisa Tagihan</span>
                                <p class="font-bold text-status-menunggak text-xs">
                                    Rp {{ number_format($student->outstanding_debt, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <!-- Micro Progress Bar -->
                        <div class="w-full bg-slate-100 rounded-full h-1 overflow-hidden">
                            <div class="bg-gradient-to-r from-teal-accent to-emerald-500 h-1 rounded-full transition-all duration-300" style="width: {{ $totalPeriods > 0 ? round(($paidCount / $totalPeriods) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Compact Period Checklist Grid -->
                    <div class="pt-2.5">
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
        <div id="tour-wali-checklist-matrix" class="card overflow-hidden hidden md:block">
            <div class="p-4 sm:p-5 overflow-x-auto w-full">
                <table id="studentsTable" class="w-full table-clean text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-navy/5 text-navy border-b border-navy/10">
                            <th class="w-8 text-center !py-2.5 !px-1 font-bold text-[11px] uppercase tracking-wider">No</th>
                            <th class="min-w-[130px] max-w-[160px] !py-2.5 !px-2 font-bold text-[11px] uppercase tracking-wider">Nama Siswa</th>
                            <th class="min-w-[70px] !py-2.5 !px-1.5 font-bold text-[11px] uppercase tracking-wider">NIS</th>
                            @foreach($periods as $period)
                                <th class="text-center min-w-[36px] lg:min-w-[42px] !px-0.5 !py-2 font-bold text-[10px] uppercase tracking-wider">
                                    <span class="block leading-tight text-slate-700 font-extrabold">{{ $period }}</span>
                                </th>
                            @endforeach
                            <th class="min-w-[95px] !py-2.5 !px-2 text-right font-bold text-[11px] uppercase tracking-wider">Total Dibayar</th>
                            <th class="min-w-[95px] !py-2.5 !px-2 text-right font-bold text-[11px] uppercase tracking-wider">Belum Dibayar</th>
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
                                <td class="font-semibold text-status-lunas text-xs !py-2 !px-2 text-right whitespace-nowrap">
                                    Rp {{ number_format($student->contributed, 0, ',', '.') }}
                                </td>
                                <td class="font-semibold text-status-menunggak text-xs !py-2 !px-2 text-right whitespace-nowrap">
                                    Rp {{ number_format($student->outstanding_debt, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($periods) + 4 }}" class="text-center py-10 text-gray-400">
                                    <span class="material-symbols-outlined text-3xl mb-1 text-gray-300">group_off</span>
                                    <p class="text-sm">Belum ada data siswa di kelas ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<script>
$(document).ready(function() {
    if ($('#studentsTable tbody tr').length > 1 || !$('#studentsTable tbody td[colspan]').length) {
        const totalCols = $('#studentsTable thead th').length;
        const nonSortableTargets = [0];
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
