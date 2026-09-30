<x-app-layout>
@section('page-title', 'Dashboard Bendahara')

<div class="space-y-6">
    <!-- Welcome Header Card -->
    <div class="card p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold mb-2">
                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                <span>Tahun Ajaran Aktif</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-heading">
                Selamat Datang! <span class="text-navy">{{ $user->name }}</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                Ringkasan kas, mutasi transaksi, dan administrasi keuangan kelas <span class="font-semibold text-slate-700">{{ $kelas ? $kelas->nama_kelas : 'Anda' }}</span>.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('bendahara.students.index') }}" class="py-2.5 px-4 text-xs sm:text-sm inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold border border-slate-200 rounded-xl transition-all">
                <span class="material-symbols-outlined text-base text-slate-500">fact_check</span>
                <span>Checklist Kas</span>
            </a>
            <a href="{{ route('bendahara.transactions.create') }}" class="btn-navy py-2.5 px-4 text-xs sm:text-sm inline-flex items-center gap-1.5 rounded-xl transition-all">
                <span class="material-symbols-outlined text-base">add</span>
                <span>Catat Transaksi</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards (3 Columns) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Saldo Kas -->
        <div id="tour-bendahara-stat-saldo" class="card p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Saldo Kas Saat Ini</p>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">account_balance_wallet</span>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading">
                    Rp {{ number_format($saldoKas, 0, ',', '.') }}
                </h3>
                <p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs text-teal-600">school</span>
                    Kelas {{ $kelas ? $kelas->nama_kelas : '-' }}
                </p>
            </div>
        </div>

        <!-- Pemasukan Bulan Ini -->
        <div class="card p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kas Masuk Bulan Ini</p>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">arrow_downward</span>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-600 font-heading">
                    Rp {{ number_format($masukBulanIni, 0, ',', '.') }}
                </h3>
                <p class="text-xs text-slate-400 mt-1.5">
                    Akumulasi: <strong class="text-slate-600 font-semibold">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</strong>
                </p>
            </div>
        </div>

        <!-- Pengeluaran Bulan Ini -->
        <div class="card p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pengeluaran Bulan Ini</p>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">arrow_upward</span>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl sm:text-3xl font-extrabold text-rose-600 font-heading">
                    Rp {{ number_format($keluarBulanIni, 0, ',', '.') }}
                </h3>
                <p class="text-xs text-slate-400 mt-1.5">
                    Akumulasi: <strong class="text-slate-600 font-semibold">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</strong>
                </p>
            </div>
        </div>
    </div>

    <!-- Main Section: Chart & Quick Action Shortcuts -->
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Cash Flow Chart (7 Cols) -->
        <div class="w-full lg:w-7/12 card p-5 sm:p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 font-heading">Tren Arus Kas (6 Bulan Terakhir)</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Perbandingan pemasukan dan pengeluaran kas kelas.</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-teal-500"></span> Masuk
                    </span>
                    <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Keluar
                    </span>
                </div>
            </div>
            <div id="bendaharaCashflowChart" class="w-full h-64 sm:h-72"></div>
        </div>

        <!-- Quick Action Shortcuts (5 Cols) -->
        <div class="w-full lg:w-5/12 card p-5 sm:p-6 flex flex-col justify-between">
            <div class="mb-4">
                <h3 class="text-base font-bold text-slate-900 font-heading">Akses Cepat Fitur</h3>
                <p class="text-xs text-slate-400 mt-0.5">Menu utama untuk mempermudah tugas pembukuan Anda.</p>
            </div>

            <div class="space-y-3">
                <a href="{{ route('bendahara.students.index') }}" class="p-3.5 rounded-xl border border-slate-200/80 bg-white hover:border-slate-300 hover:bg-slate-50/50 transition-all flex items-center gap-3.5 group">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-xl">fact_check</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-navy truncate font-heading">Checklist & Presensi Kas</h4>
                        <p class="text-[11px] text-slate-400 truncate">Centang iuran mingguan / bulanan siswa</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-300 group-hover:text-slate-600 group-hover:translate-x-0.5 transition-all text-lg">chevron_right</span>
                </a>

                <a href="{{ route('bendahara.transactions.index') }}" class="p-3.5 rounded-xl border border-slate-200/80 bg-white hover:border-slate-300 hover:bg-slate-50/50 transition-all flex items-center gap-3.5 group">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-xl">receipt_long</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-navy truncate font-heading">Buku Kas & Riwayat Mutasi</h4>
                        <p class="text-[11px] text-slate-400 truncate">Catat transaksi pemasukan & pengeluaran</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-300 group-hover:text-slate-600 group-hover:translate-x-0.5 transition-all text-lg">chevron_right</span>
                </a>

                <a href="{{ route('bendahara.report.index') }}" class="p-3.5 rounded-xl border border-slate-200/80 bg-white hover:border-slate-300 hover:bg-slate-50/50 transition-all flex items-center gap-3.5 group">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-xl">bar_chart</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-navy truncate font-heading">Laporan Keuangan & Ekspor</h4>
                        <p class="text-[11px] text-slate-400 truncate">Unduh rekap kas format PDF & Excel</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-300 group-hover:text-slate-600 group-hover:translate-x-0.5 transition-all text-lg">chevron_right</span>
                </a>

                <a href="{{ route('bendahara.transactions.logs') }}" class="p-3.5 rounded-xl border border-slate-200/80 bg-white hover:border-slate-300 hover:bg-slate-50/50 transition-all flex items-center gap-3.5 group">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-xl">history</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-navy truncate font-heading">Riwayat Log Transaksi</h4>
                        <p class="text-[11px] text-slate-400 truncate">Pantau catatan penambahan, edit, dan hapus</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-300 group-hover:text-slate-600 group-hover:translate-x-0.5 transition-all text-lg">chevron_right</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table Section -->
    <div class="card p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 font-heading">5 Transaksi Terakhir</h3>
                <p class="text-xs text-slate-400 mt-0.5">Aktivitas mutasi pemasukan dan pengeluaran kas terbaru.</p>
            </div>
            <a href="{{ route('bendahara.transactions.index') }}" class="text-xs font-semibold text-navy hover:underline inline-flex items-center gap-1">
                <span>Lihat Semua Transaksi</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        @if($recentTransactions->count() > 0)
            <div class="p-1 sm:p-3 overflow-x-visible sm:overflow-x-auto w-full">
                <table class="w-full text-left table-clean table-responsive-cards recent-transactions-table text-xs sm:text-sm">
                    <thead>
                        <tr class="bg-slate-50/80">
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>Jenis</th>
                            <th class="text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentTransactions as $tx)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td data-label="Tanggal" class="font-mono text-xs text-slate-600">
                                    {{ $tx->tanggal_transaksi ? $tx->tanggal_transaksi->format('d/m/Y') : '-' }}
                                </td>
                                <td data-label="Kategori" class="text-xs font-medium text-slate-700">
                                    {{ $tx->kategori ? $tx->kategori->nama_kategori : 'Kas Umum' }}
                                </td>
                                <td data-label="Keterangan" class="text-xs text-slate-600">
                                    {{ $tx->keterangan ?? '-' }}
                                </td>
                                <td data-label="Jenis">
                                    @if($tx->jenis_transaksi === 'pemasukan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Masuk
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Keluar
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Nominal" class="text-xs font-bold font-mono text-right {{ $tx->jenis_transaksi === 'pemasukan' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $tx->jenis_transaksi === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($tx->total_nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-400 text-xs sm:text-sm">
                <span class="material-symbols-outlined text-4xl text-gray-300 mb-1">receipt_long</span>
                <p>Belum ada catatan mutasi transaksi kas di kelas ini.</p>
                <a href="{{ route('bendahara.transactions.create') }}" class="text-xs font-semibold text-teal-accent hover:underline mt-2 inline-block">
                    + Catat Transaksi Pertama Sekarang
                </a>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    /* Responsive Mobile Card View for Recent Transactions Table */
    @media (max-width: 767.98px) {
        .recent-transactions-table, 
        .recent-transactions-table tbody, 
        .recent-transactions-table tr, 
        .recent-transactions-table td {
            display: block;
            width: 100% !important;
            box-sizing: border-box;
        }
        .recent-transactions-table thead {
            display: none;
        }
        .recent-transactions-table tr {
            margin-bottom: 0.875rem;
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }
        .recent-transactions-table td {
            padding: 0.35rem 0 !important;
            border: none !important;
            text-align: left !important;
        }
        .recent-transactions-table td::before {
            content: attr(data-label);
            display: block;
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748B;
            font-weight: 600;
            margin-bottom: 0.15rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartElement = document.querySelector("#bendaharaCashflowChart");
        if (!chartElement) return;

        const months = @json($months);
        const incomeData = @json($chartIncome);
        const expenseData = @json($chartExpense);

        const options = {
            series: [
                {
                    name: 'Kas Masuk',
                    data: incomeData
                },
                {
                    name: 'Pengeluaran',
                    data: expenseData
                }
            ],
            chart: {
                type: 'area',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'Work Sans, sans-serif'
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
                    opacityFrom: 0.35,
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
                    formatter: function(val) {
                        if (val >= 1000000) return 'Rp ' + (val/1000000).toFixed(1) + 'M';
                        if (val >= 1000) return 'Rp ' + (val/1000).toFixed(0) + 'k';
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
                    formatter: function(val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            },
            legend: { show: false },
            responsive: [{
                breakpoint: 640,
                options: {
                    chart: { height: 230 },
                    yaxis: { labels: { show: false } }
                }
            }]
        };

        const chart = new ApexCharts(chartElement, options);
        chart.render();
    });
</script>
@endpush
</x-app-layout>