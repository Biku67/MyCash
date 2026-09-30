<x-app-layout>
@section('page-title', 'Dashboard Wali Kelas')

<div class="space-y-6">
    <!-- Welcome Header Card -->
    <div class="card p-5 sm:p-6 border border-slate-200/80 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">
                Selamat Datang! <span class="text-[#1B4F72]">{{ $user->name }}</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pantau iuran siswa dan mutasi kas pada kelas yang Anda ampu secara transparan.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <span class="text-xs font-medium px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-600 inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm text-slate-500">calendar_today</span>
                <span>{{ now()->translatedFormat('l, d F Y') }}</span>
            </span>
            <a href="{{ route('wali-kelas.students.index') }}" class="btn-navy py-2.5 px-4 text-xs sm:text-sm font-semibold rounded-xl inline-flex items-center gap-1.5 shadow-sm transition-all">
                <span class="material-symbols-outlined text-base">groups</span>
                <span>Daftar Siswa</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards (3 Columns) -->
    <div id="tour-wali-stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Kelas Yang Diampu -->
        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-slate-500">Kelas yang Diampu</p>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">school</span>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-extrabold text-slate-900 font-heading">{{ $kelasListWithStats->count() }} Kelas</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Kelas aktif binaan Anda</p>
            </div>
        </div>

        <!-- Total Siswa Binaan -->
        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-slate-500">Total Siswa Binaan</p>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">groups</span>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-extrabold text-slate-900 font-heading">{{ $totalSiswa }} Siswa</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Jumlah seluruh siswa terdaftar</p>
            </div>
        </div>

        <!-- Total Kas Terkumpul -->
        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-slate-500">Total Saldo Kas</p>
                <div class="w-10 h-10 rounded-xl bg-[#1B4F72] text-white flex items-center justify-center shadow-sm">
                    <span class="material-symbols-outlined text-xl">account_balance_wallet</span>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-extrabold text-slate-900 font-heading">
                    Rp {{ number_format($totalKasTerkumpul, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Akumulasi kas seluruh kelas</p>
            </div>
        </div>

        <!-- Tingkat Partisipasi Iuran -->
        {{-- <div class="card p-5 border border-gray-100 hover:border-emerald-200 transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Kelunasan Iuran</p>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">task_alt</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="flex items-baseline justify-between">
                    <h3 class="text-2xl font-extrabold text-navy font-heading">{{ $complianceRate }}%</h3>
                    <span class="text-xs text-gray-500 font-medium">{{ $siswaPaidCount }}/{{ $totalSiswa }} Siswa</span>
                </div>
                <!-- Progress Bar -->
                <div class="w-full bg-gray-100 rounded-full h-2 mt-2 overflow-hidden">
                    <div class="bg-teal-accent h-2 rounded-full transition-all duration-500" style="width: {{ $complianceRate }}%"></div>
                </div>
            </div>
        </div> --}}
    </div>

    <!-- Main Section: Kelas Binaan List & Status Kelunasan Donut Chart -->
    {{-- <div class="flex flex-col lg:flex-row gap-6">
        <!-- Daftar Kelas Binaan Cards (7 Cols) -->
        <div class="w-full lg:w-7/12 card p-5 sm:p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-navy" style="font-family:'Manrope',sans-serif;">Kelas yang Anda Ampu</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Rincian status kas dan bendahara di masing-masing kelas.</p>
                </div>
            </div>

            @if($kelasListWithStats->count() > 0)
                <div class="space-y-3.5">
                    @foreach($kelasListWithStats as $kelas)
                        <div class="p-4 rounded-xl border border-gray-100 bg-white hover:border-teal-accent/50 hover:shadow-sm transition-all flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded bg-navy/10 text-navy font-mono font-bold text-xs uppercase">{{ $kelas->kode_kelas }}</span>
                                    <h4 class="font-bold text-gray-900 text-sm sm:text-base">{{ $kelas->nama_kelas }}</h4>
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700">
                                        {{ $kelas->total_siswa }} Siswa
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">
                                    Bendahara: <strong>{{ $kelas->bendahara->count() > 0 ? $kelas->bendahara->pluck('nama')->implode(', ') : 'Belum ditugaskan' }}</strong>
                                </p>
                                <p class="text-xs text-gray-400">
                                    Iuran Standar: Rp {{ number_format($kelas->nominal_standar, 0, ',', '.') }} / {{ $kelas->tipe_periode }}
                                </p>
                            </div>
                            <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center border-t sm:border-t-0 pt-2 sm:pt-0 border-gray-100">
                                <p class="text-[11px] text-gray-400 uppercase tracking-wider">Saldo Kas Kelas</p>
                                <p class="text-base sm:text-lg font-bold font-mono text-emerald-600">
                                    Rp {{ number_format($kelas->saldo_kas, 0, ',', '.') }}
                                </p>
                                <a href="{{ route('wali-kelas.students.index') }}" class="mt-1.5 text-xs font-semibold text-navy hover:text-teal-accent inline-flex items-center gap-1">
                                    <span>Lihat Siswa</span>
                                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-gray-400 text-xs sm:text-sm">
                    <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">school</span>
                    <p>Anda belum memiliki kelas yang diampu.</p>
                    <p class="text-gray-400 mt-1">Silakan hubungi admin untuk pengaturan kelas Anda.</p>
                </div>
            @endif
        </div>

        <!-- Donut Chart & Monitoring Alert (5 Cols) -->
        <div class="w-full lg:w-5/12 card p-5 sm:p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
            <div class="mb-4">
                <h3 class="text-base font-bold text-navy" style="font-family:'Manrope',sans-serif;">Status Partisipasi Iuran Kas</h3>
                <p class="text-xs text-gray-400 mt-0.5">Proporsi siswa yang telah berpartisipasi dan menunggak.</p>
            </div>

            <div id="waliComplianceChart" class="w-full h-56 flex items-center justify-center"></div>

            <div class="mt-4 p-3.5 rounded-xl bg-blue-50/80 border border-blue-100 flex items-start gap-2.5">
                <span class="material-symbols-outlined text-blue-600 text-lg flex-shrink-0 mt-0.5">info</span>
                <div>
                    <h5 class="text-xs font-bold text-blue-900">Peran Pengawasan Wali Kelas</h5>
                    <p class="text-[11px] text-blue-700 mt-0.5 leading-relaxed">
                        Anda dapat memantau kedisiplinan iuran kas siswa secara berkala dan berkoordinasi dengan Bendahara kelas apabila terdapat tunggakan.
                    </p>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Recent Homeroom Transactions Table -->
    <div class="card p-5 sm:p-6 border border-slate-200/80 rounded-2xl shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 font-heading">5 Transaksi Terakhir</h3>
                <p class="text-xs text-slate-500 mt-0.5">Catatan kas terbaru yang diinput oleh bendahara kelas binaan Anda.</p>
            </div>
        </div>

        @if($recentTransactions->count() > 0)
            <div class="p-2 sm:p-5 overflow-x-visible sm:overflow-x-auto w-full">
                <table class="w-full text-left table-clean table-responsive-cards recent-wali-transactions text-xs sm:text-sm">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kelas</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>Bendahara</th>
                            <th>Jenis</th>
                            <th class="text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentTransactions as $tx)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td data-label="Tanggal" class="font-medium text-slate-600">
                                    {{ $tx->tanggal_transaksi ? $tx->tanggal_transaksi->format('d M Y') : '-' }}
                                </td>
                                <td data-label="Kelas">
                                    <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $tx->kelas ? $tx->kelas->nama_kelas : $tx->kode_kelas }}
                                    </span>
                                </td>
                                <td data-label="Kategori" class="text-xs font-medium text-slate-700">
                                    {{ $tx->kategori ? $tx->kategori->nama_kategori : 'Kas Umum' }}
                                </td>
                                <td data-label="Keterangan" class="text-xs text-slate-600">
                                    {{ $tx->keterangan ?? '-' }}
                                </td>
                                <td data-label="Bendahara" class="text-xs text-slate-600">
                                    {{ $tx->bendahara ? $tx->bendahara->nama : '-' }}
                                </td>
                                <td data-label="Jenis">
                                    @if($tx->jenis_transaksi === 'pemasukan')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                            Masuk
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200/60">
                                            Keluar
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Nominal" class="text-xs font-bold text-right font-heading {{ $tx->jenis_transaksi === 'pemasukan' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $tx->jenis_transaksi === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($tx->total_nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-10 text-slate-400">
                <span class="material-symbols-outlined text-4xl mb-1 text-slate-300">receipt_long</span>
                <p class="text-xs text-slate-500">Belum ada catatan transaksi kas di kelas Anda.</p>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    /* Responsive Mobile Card View for Wali Kelas Tables */
    @media (max-width: 767.98px) {
        .recent-wali-transactions,
        .recent-wali-transactions tbody,
        .recent-wali-transactions tr,
        .recent-wali-transactions td {
            display: block;
            width: 100% !important;
            box-sizing: border-box;
        }
        .recent-wali-transactions thead {
            display: none;
        }
        .recent-wali-transactions tr {
            margin-bottom: 0.875rem;
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }
        .recent-wali-transactions td {
            padding: 0.35rem 0 !important;
            border: none !important;
            text-align: left !important;
        }
        .recent-wali-transactions td::before {
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
        const paidCount = {{ $siswaPaidCount }};
        const unpaidCount = {{ $siswaUnpaidCount }};

        const chartEl = document.querySelector("#waliComplianceChart");
        if (chartEl && (paidCount > 0 || unpaidCount > 0)) {
            const options = {
                series: [paidCount, unpaidCount],
                labels: ['Sudah Bayar', 'Belum / Menunggak'],
                chart: {
                    type: 'donut',
                    height: 220,
                    fontFamily: 'Work Sans, sans-serif'
                },
                colors: ['#5DCAA5', '#F43F5E'],
                legend: {
                    position: 'bottom',
                    fontSize: '11px',
                    fontFamily: 'Work Sans',
                    labels: { colors: '#64748B' }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toFixed(0) + "%";
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Siswa',
                                    fontSize: '11px',
                                    color: '#64748B',
                                    formatter: function(w) {
                                        return {{ $totalSiswa }} + ' Anak';
                                    }
                                }
                            }
                        }
                    }
                }
            };
            const chart = new ApexCharts(chartEl, options);
            chart.render();
        } else if (chartEl) {
            chartEl.innerHTML = '<p class="text-xs text-gray-400 py-8">Belum ada data siswa.</p>';
        }
    });
</script>
@endpush
</x-app-layout>
