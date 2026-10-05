<x-app-layout>
@section('page-title', 'Laporan Keuangan Kas')

<div class="space-y-6">
    <!-- Header Page Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">Laporan Keuangan Kas</h1>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                    Kelas {{ $kelas->nama_kelas ?? $kelas->kode_kelas }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Rekapitulasi arus kas masuk, pengeluaran, serta mutasi pembukuan kas kelas secara transparan.
            </p>
        </div>

        <div id="tour-bendahara-report-actions" class="flex flex-wrap items-center gap-2.5">
            <a id="btnExportExcel" 
               href="{{ route('bendahara.report.exportExcel', array_filter(['start_date' => $startDate, 'end_date' => $endDate, 'category_id' => $categoryId, 'type' => $type])) }}" 
               title="Unduh Buku Kas Umum ke Excel (.xlsx)"
               class="py-2.5 px-3 text-xs inline-flex items-center gap-1.5 bg-white text-emerald-700 border border-slate-200 hover:bg-emerald-50 rounded-xl font-semibold transition-all">
                <span class="material-symbols-outlined text-base">table</span> Ekspor Excel
            </a>
            <a id="btnExportPdf" 
               href="{{ route('bendahara.report.exportPdf', array_filter(['start_date' => $startDate, 'end_date' => $endDate, 'category_id' => $categoryId, 'type' => $type])) }}" 
               target="_blank"
               title="Cetak Buku Kas PDF (Buka di Tab Baru)"
               class="py-2.5 px-3 text-xs inline-flex items-center gap-1.5 bg-white text-rose-700 border border-slate-200 hover:bg-rose-50 rounded-xl font-semibold transition-all">
                <span class="material-symbols-outlined text-base">picture_as_pdf</span> Cetak PDF
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div id="tour-bendahara-report-filter" class="card p-5 sm:p-6 border border-slate-200/80 rounded-2xl shadow-sm" x-data="reportFilterForm()">
        <form method="GET" action="{{ route('bendahara.report.index') }}" id="filterForm" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Dari Tanggal Transaksi</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none z-10">event</span>
                        <input type="text" name="start_date" id="start_date" value="{{ $startDate }}" max="{{ date('Y-m-d') }}" class="datepicker-report input-clean w-full pl-9 pr-3 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="Pilih tanggal awal...">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Sampai Tanggal Transaksi</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none z-10">event</span>
                        <input type="text" name="end_date" id="end_date" value="{{ $endDate }}" max="{{ date('Y-m-d') }}" class="datepicker-report input-clean w-full pl-9 pr-3 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl" placeholder="Pilih tanggal akhir...">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jenis Transaksi</label>
                    <select name="type" id="typeSelect" x-model="selectedType" class="ui selection dropdown input-clean w-full">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Semua (Pemasukan & Pengeluaran)</option>
                        <option value="pemasukan" {{ $type === 'pemasukan' ? 'selected' : '' }}>Hanya Pemasukan</option>
                        <option value="pengeluaran" {{ $type === 'pengeluaran' ? 'selected' : '' }}>Hanya Pengeluaran</option>
                    </select>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-600">Kategori Transaksi</label>
                        <span x-show="selectedType === 'all'" x-cloak class="text-[10px] text-amber-600 font-medium flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-xs">info</span> Pilih jenis dahulu
                        </span>
                    </div>
                    <select name="category_id" id="categorySelect" x-model="selectedCategoryId" :disabled="selectedType === 'all'" class="ui selection dropdown input-clean w-full" :class="{ 'disabled opacity-100 cursor-not-allowed': selectedType === 'all' }">
                        <template x-if="selectedType === 'all'">
                            <option value="">Pilih Jenis Transaksi Dulu</option>
                        </template>
                        <template x-if="selectedType !== 'all'">
                            <option value="" x-text="selectedType === 'pemasukan' ? 'Semua Kategori Pemasukan' : 'Semua Kategori Pengeluaran'"></option>
                        </template>
                        <template x-for="cat in filteredCategories" :key="cat.id">
                            <option :value="cat.id" x-text="cat.nama_kategori" :selected="selectedCategoryId == cat.id"></option>
                        </template>
                    </select>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100">
                <!-- Quick Presets -->
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="text-slate-400 font-medium">Filter Cepat:</span>
                    <button type="button" onclick="setPreset('this_month')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition-colors text-xs">
                        Bulan Ini
                    </button>
                    <button type="button" onclick="setPreset('last_month')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition-colors text-xs">
                        Bulan Lalu
                    </button>
                    <button type="button" onclick="setPreset('this_year')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition-colors text-xs">
                        Tahun Ini
                    </button>
                </div>

                <div class="flex items-center gap-2 self-end sm:self-auto">
                    <a href="{{ route('bendahara.report.index') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-all">
                        Reset Filter
                    </a>
                    <button type="submit" class="btn-navy py-2 px-4 text-xs font-semibold rounded-xl inline-flex items-center gap-1.5 shadow-sm">
                        <span class="material-symbols-outlined text-sm">filter_alt</span> Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- 4 Summary Metric Cards (Buku Kas Umum) -->
    <div id="tour-bendahara-report-stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        <!-- Total Pemasukan -->
        <div class="card p-5 border border-emerald-100 rounded-2xl relative overflow-hidden bg-white shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Total Pemasukan</p>
                    <p class="text-xl sm:text-2xl font-black text-emerald-600 font-heading mt-1">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </p>
                    <p class="text-[11px] text-slate-400 mt-1">Pada rentang tanggal dipilih</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-2xl">arrow_downward</span>
                </div>
            </div>
        </div>

        <!-- Total Pengeluaran -->
        <div class="card p-5 border border-rose-100 rounded-2xl relative overflow-hidden bg-white shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Total Pengeluaran</p>
                    <p class="text-xl sm:text-2xl font-black text-rose-600 font-heading mt-1">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </p>
                    <p class="text-[11px] text-slate-400 mt-1">Pada rentang tanggal dipilih</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-2xl">arrow_upward</span>
                </div>
            </div>
        </div>

        <!-- Saldo Kas Akhir -->
        <div class="card p-5 border border-teal-200 rounded-2xl relative overflow-hidden bg-gradient-to-br from-white to-teal-50/40 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-teal-700 uppercase tracking-wider">Saldo Kas Akhir</p>
                    <p class="text-xl sm:text-2xl font-black text-teal-800 font-heading mt-1">
                        Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                    </p>
                    <p class="text-[11px] text-teal-600 font-medium mt-1">
                        Arus Bersih: {{ $netFlow >= 0 ? '+' : '' }}Rp {{ number_format($netFlow, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-2xl">savings</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Trend Arus Kas Bulanan -->
    <div id="tour-bendahara-report-chart" class="card p-5 sm:p-6 border border-slate-200/80 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base font-heading">Trend Arus Kas Bulanan</h3>
                <p class="text-xs text-slate-400">Grafik perbandingan pemasukan dan pengeluaran kas kelas setiap bulan</p>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <span class="inline-flex items-center gap-1.5 text-slate-600 font-medium">
                    <span class="w-3 h-3 rounded-full bg-[#5DCAA5]"></span> Pemasukan
                </span>
                <span class="inline-flex items-center gap-1.5 text-slate-600 font-medium">
                    <span class="w-3 h-3 rounded-full bg-[#F43F5E]"></span> Pengeluaran
                </span>
            </div>
        </div>
        <div id="cashFlowChart" style="min-height: 280px;"></div>
    </div>

    <!-- Transaction Table: Buku Mutasi Kas -->
    <div id="tour-bendahara-report-table" class="card overflow-hidden shadow-sm">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base font-heading">Buku Mutasi Kas</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar transaksi kas berurutan dengan kalkulasi saldo berjalan</p>
            </div>
            <span class="px-3 py-1 bg-slate-100 text-slate-600 font-semibold text-xs rounded-full self-start sm:self-auto">
                {{ $transactions->count() }} Transaksi Tercatat
            </span>
        </div>

        <div class="p-2 sm:p-5 overflow-x-visible sm:overflow-x-auto w-full">
            <table id="transactionsTable" class="w-full table-clean table-responsive-cards text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr>
                        <th class="w-10 text-center">No</th>
                        <th class="w-28">Tanggal</th>
                        <th class="min-w-[130px]">Kategori</th>
                        <th>Deskripsi</th>
                        <th class="w-24 text-center">Tipe</th>
                        <th class="text-right w-32">Jumlah</th>
                        <th class="text-right w-36">Saldo Berjalan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td data-label="No" class="w-10 text-center text-xs text-slate-400 font-mono">
                                {{ $loop->iteration }}
                            </td>
                            <td data-label="Tanggal" class="whitespace-nowrap text-xs sm:text-sm font-medium text-slate-700">
                                {{ \Carbon\Carbon::parse($tx->tanggal_transaksi)->translatedFormat('d M Y') }}
                            </td>
                            <td data-label="Kategori" class="whitespace-nowrap">
                                @php
                                    $catName = $tx->kategori->nama_kategori ?? '-';
                                @endphp
                                @if($catName === 'Uang Kas')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-teal-50 text-teal-700 border border-teal-200/60 inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>{{ $catName }}
                                    </span>
                                @elseif($tx->jenis_transaksi === 'pemasukan')
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $catName }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-slate-100 text-slate-700 border border-slate-200/60 inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>{{ $catName }}
                                    </span>
                                @endif
                            </td>
                            <td data-label="Deskripsi" class="text-xs sm:text-sm text-slate-800">
                                <span class="font-medium text-slate-900">{{ $tx->keterangan ?? '-' }}</span>
                                <br>
                                @if($tx->detailTransaksi->isNotEmpty())
                                    @php
                                        $studentNames = $tx->detailTransaksi->pluck('siswa.nama')->filter()->join(', ');
                                    @endphp
                                    @if($studentNames)
                                        <div class="text-[11px] text-slate-400 mt-0.5 inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">person</span>
                                            <span>Siswa: {{ $studentNames }}</span>
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td data-label="Tipe" class="whitespace-nowrap text-center">
                                @if($tx->jenis_transaksi === 'pemasukan')
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">arrow_downward</span>Pemasukan
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-rose-50 text-rose-600 border border-rose-200/60 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">arrow_upward</span>Pengeluaran
                                    </span>
                                @endif
                            </td>
                            <td data-label="Jumlah" class="whitespace-nowrap text-right text-xs sm:text-sm font-bold {{ $tx->jenis_transaksi === 'pemasukan' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $tx->jenis_transaksi === 'pemasukan' ? '+' : '-' }}Rp {{ number_format($tx->total_nominal, 0, ',', '.') }}
                            </td>
                            <td data-label="Saldo Berjalan" class="whitespace-nowrap text-right text-xs sm:text-sm font-bold text-slate-900 font-heading">
                                Rp {{ number_format($tx->running_balance, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">receipt_long</span>
                                Tidak ada transaksi yang sesuai dengan filter pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Render ApexCharts for Cash Flow Trend
    const chartEl = document.querySelector("#cashFlowChart");
    const months = @json($months ?? []);
    const incomeData = @json($incomeData ?? []);
    const expenseData = @json($expenseData ?? []);

    if (chartEl && months.length > 0) {
        const chartOptions = {
            series: [
                {
                    name: 'Pemasukan Kas',
                    data: incomeData
                },
                {
                    name: 'Pengeluaran Kas',
                    data: expenseData
                }
            ],
            chart: {
                type: 'area',
                height: 280,
                fontFamily: 'Work Sans, sans-serif',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: ['#5DCAA5', '#F43F5E'],
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 2.5
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.40,
                    opacityTo: 0.05,
                    stops: [0, 95, 100]
                }
            },
            xaxis: {
                categories: months,
                labels: {
                    style: { colors: '#94A3B8', fontSize: '11px', fontFamily: 'Work Sans' }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#94A3B8', fontSize: '11px', fontFamily: 'Work Sans' },
                    formatter: function (val) {
                        if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
                        if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'k';
                        return 'Rp ' + val;
                    }
                }
            },
            grid: {
                borderColor: '#F1F5F9',
                strokeDashArray: 4
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                fontSize: '12px',
                fontFamily: 'Work Sans',
                markers: { radius: 12 }
            },
            responsive: [{
                breakpoint: 640,
                options: {
                    chart: { height: 230 },
                    yaxis: { labels: { show: false } }
                }
            }]
        };

        const chart = new ApexCharts(chartEl, chartOptions);
        chart.render();
    }

    // DataTable initialization
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $.fn.dataTable.ext.errMode = 'none';

        if ($('#transactionsTable tbody tr').length > 0 && !$('#transactionsTable tbody td[colspan]').length) {
            $('#transactionsTable').DataTable({
                createdRow: function(row, data, dataIndex) {
                    const labels = ['No', 'Tanggal', 'Kategori', 'Deskripsi', 'Tipe', 'Jumlah', 'Saldo Berjalan'];
                    $('td', row).each(function(i) {
                        if (labels[i]) $(this).attr('data-label', labels[i]);
                    });
                },
                language: {
                    search: "",
                    searchPlaceholder: "Cari transaksi laporan...",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ transaksi",
                    infoEmpty: "Menampilkan 0 transaksi",
                    infoFiltered: "(disaring dari _MAX_ total)",
                    paginate: {
                        first: "«",
                        last: "»",
                        next: "›",
                        previous: "‹"
                    },
                    emptyTable: "Belum ada transaksi pada periode ini",
                    zeroRecords: "Tidak ada transaksi yang cocok"
                },
                dom: '<"flex flex-col sm:flex-row justify-between items-stretch sm:items-center mb-3 gap-2.5"lf>rt<"flex flex-col sm:flex-row justify-between items-center mt-3 gap-2.5"ip>',
                ordering: false,
                pageLength: 20,
                drawCallback: function() {
                    $('.dataTables_paginate').addClass('flex justify-center sm:justify-end gap-1 mt-2 sm:mt-0');
                }
            });
        }
    }
});

function setPreset(type) {
    const now = new Date();
    let start, end;

    if (type === 'this_month') {
        start = new Date(now.getFullYear(), now.getMonth(), 1);
        end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    } else if (type === 'last_month') {
        start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
        end = new Date(now.getFullYear(), now.getMonth(), 0);
    } else if (type === 'this_year') {
        start = new Date(now.getFullYear(), 0, 1);
        end = new Date(now.getFullYear(), 11, 31);
    }

    if (end > now) {
        end = now;
    }

    const pad = (n) => String(n).padStart(2, '0');
    const startVal = `${start.getFullYear()}-${pad(start.getMonth() + 1)}-${pad(start.getDate())}`;
    const endVal = `${end.getFullYear()}-${pad(end.getMonth() + 1)}-${pad(end.getDate())}`;

    const startEl = document.getElementById('start_date');
    const endEl = document.getElementById('end_date');

    if (startEl && startEl._flatpickr) {
        startEl._flatpickr.setDate(startVal, false);
    } else if (startEl) {
        startEl.value = startVal;
    }

    if (endEl && endEl._flatpickr) {
        endEl._flatpickr.setDate(endVal, false);
    } else if (endEl) {
        endEl.value = endVal;
    }

    document.getElementById('filterForm').submit();
}

function reportFilterForm() {
    return {
        selectedType: '{{ $type }}',
        selectedCategoryId: '{{ $categoryId ?? '' }}',
        categories: @json($categories),

        get filteredCategories() {
            if (this.selectedType === 'all') return [];
            return this.categories.filter(c => c.tipe === this.selectedType);
        },

        init() {
            this.$nextTick(() => {
                this.syncCategoryDropdown();
            });

            this.$watch('selectedType', (newType) => {
                const currentCat = this.categories.find(c => c.id == this.selectedCategoryId);
                if (newType === 'all' || (currentCat && currentCat.tipe !== newType)) {
                    this.selectedCategoryId = '';
                }
                this.$nextTick(() => {
                    this.syncCategoryDropdown();
                });
            });
        },

        syncCategoryDropdown() {
            const $cat = $('#categorySelect');
            if (!$cat.length) return;
            const $dropdown = $cat.closest('.ui.dropdown');

            if (this.selectedType === 'all') {
                $cat.prop('disabled', true);
                $dropdown.addClass('disabled');
                if (typeof $cat.dropdown === 'function') {
                    $cat.dropdown('set text', 'Pilih Jenis Transaksi Dulu');
                    $cat.dropdown('set value', '');
                }
            } else {
                $cat.prop('disabled', false);
                $dropdown.removeClass('disabled');
                if (typeof $cat.dropdown === 'function') {
                    $cat.dropdown('refresh');
                    if (this.selectedCategoryId) {
                        $cat.dropdown('set selected', this.selectedCategoryId);
                    } else {
                        const defaultText = this.selectedType === 'pemasukan' 
                            ? 'Semua Kategori Pemasukan' 
                            : 'Semua Kategori Pengeluaran';
                        $cat.dropdown('set text', defaultText);
                        $cat.dropdown('set value', '');
                    }
                }
            }
        }
    };
}
</script>
@endpush
</x-app-layout>
