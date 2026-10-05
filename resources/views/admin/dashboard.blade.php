<x-app-layout>
@section('page-title', 'Dashboard Super Admin')

<div class="space-y-6">
    <!-- Welcome Header Card -->
    <div class="card p-5 sm:p-6 border border-slate-200/80 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold mb-2">
                <span class="material-symbols-outlined text-xs text-slate-600">shield_person</span>
                <span>Super Admin Panel</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight">
                Selamat Datang, <span class="text-[#1B4F72]">{{ $user->name }}</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pantau kas seluruh kelas, kelola akun bendahara, dan wali kelas dari satu tempat.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.bendahara.create') }}" class="py-2.5 px-3.5 text-xs sm:text-sm inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold shadow-sm rounded-xl border border-slate-200 transition-all">
                <span class="material-symbols-outlined text-base text-slate-600">person_add</span>
                <span>+ Bendahara</span>
            </a>
            <a href="{{ route('admin.wali-kelas.create') }}" class="py-2.5 px-3.5 text-xs sm:text-sm inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold shadow-sm rounded-xl border border-slate-200 transition-all">
                <span class="material-symbols-outlined text-base text-slate-600">supervisor_account</span>
                <span>+ Wali Kelas</span>
            </a>
            <a href="{{ route('admin.kelas.create') }}" class="btn-navy py-2.5 px-4 text-xs sm:text-sm inline-flex items-center gap-1.5 font-semibold shadow-sm rounded-xl transition-all">
                <span class="material-symbols-outlined text-base">add_home_work</span>
                <span>+ Kelas</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards (5 Grid Items) -->
    <div id="tour-admin-stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Total Kelas -->
        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-slate-500">Total Kelas</p>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">school</span>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading">{{ $totalKelas }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Ruang Kelas Aktif</p>
            </div>
        </div>

        <!-- Total Siswa Aktif -->
        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-slate-500">Siswa Terdaftar</p>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">groups</span>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading">{{ $totalSiswa }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Siswa Seluruh Kelas</p>
            </div>
        </div>

        <!-- Bendahara -->
        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-slate-500">Bendahara</p>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">manage_accounts</span>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading">{{ $totalBendahara }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Pengelola Kas Aktif</p>
            </div>
        </div>

        <!-- Wali Kelas -->
        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-slate-500">Wali Kelas</p>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg">supervisor_account</span>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading">{{ $totalWaliKelas }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Guru Pembina Kelas</p>
            </div>
        </div>
    </div>

    <!-- Charts Section: Sebaran Kas per Kelas & Distribusi Siswa -->
    {{-- <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sebaran Saldo Kas Antar Kelas (7 Cols) -->
        <div class="w-full lg:w-7/12 card p-5 sm:p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-navy" style="font-family:'Manrope',sans-serif;">Sebaran Saldo Kas Antar Kelas</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Perbandingan ketersediaan dana kas masing-masing kelas.</p>
                </div>
            </div>
            <div id="adminClassBalanceChart" class="w-full h-64 sm:h-72"></div>
        </div>

        <!-- Komposisi Siswa per Kelas (5 Cols) -->
        <div class="w-full lg:w-5/12 card p-5 sm:p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-navy" style="font-family:'Manrope',sans-serif;">Distribusi Siswa per Kelas</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Jumlah siswa yang terdaftar pada tiap kelas.</p>
                </div>
            </div>
            <div id="adminStudentDistChart" class="w-full h-64 sm:h-72 flex items-center justify-center"></div>
        </div>
    </div> --}}

    <!-- Class Financial Health Summary Table -->
    <div class="card p-5 sm:p-6 border border-slate-200/80 rounded-2xl shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 font-heading">Ringkasan Kas Seluruh Kelas</h3>
                <p class="text-xs text-slate-500 mt-0.5">Pantau saldo kas, wali kelas, dan bendahara tiap kelas secara komprehensif.</p>
            </div>
            <a href="{{ route('admin.kelas.index') }}" class="text-xs font-semibold text-[#1B4F72] hover:underline inline-flex items-center gap-1">
                <span>Kelola Data Kelas</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        @if($kelasList->count() > 0)
            <div class="p-2 sm:p-5 overflow-x-visible sm:overflow-x-auto w-full">
                <table class="w-full text-left table-clean table-responsive-cards admin-class-table text-xs sm:text-sm">
                    <thead>
                        <tr>
                            <th>Kode & Kelas</th>
                            <th>Wali Kelas</th>
                            <th>Bendahara</th>
                            <th>Siswa</th>
                            <th>Iuran Standar</th>
                            <th>Kas Masuk</th>
                            <th>Pengeluaran</th>
                            <th class="text-right">Saldo Kas</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($kelasList as $kelas)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td data-label="Kelas">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">{{ $kelas->kode_kelas }}</span>
                                    <span class="font-bold text-slate-800 text-xs sm:text-sm ml-1.5">{{ $kelas->nama_kelas }}</span>
                                </td>
                                <td data-label="Wali Kelas" class="text-xs text-slate-700">
                                    {{ $kelas->waliKelas ? $kelas->waliKelas->nama : '-' }}
                                </td>
                                <td data-label="Bendahara" class="text-xs text-slate-600">
                                    @if($kelas->bendahara->count() > 0)
                                        {{ $kelas->bendahara->pluck('nama')->implode(', ') }}
                                    @else
                                        <span class="text-amber-600 font-medium text-[11px]">Belum Ditugaskan</span>
                                    @endif
                                </td>
                                <td data-label="Siswa" class="text-xs font-semibold text-slate-700">
                                    {{ $kelas->total_siswa }} Siswa
                                </td>
                                <td data-label="Iuran Standar" class="text-xs text-slate-600">
                                    Rp {{ number_format($kelas->nominal_standar, 0, ',', '.') }} / {{ $kelas->tipe_periode }}
                                </td>
                                <td data-label="Kas Masuk" class="text-xs font-medium text-emerald-600">
                                    Rp {{ number_format($kelas->total_masuk, 0, ',', '.') }}
                                </td>
                                <td data-label="Pengeluaran" class="text-xs font-medium text-rose-600">
                                    Rp {{ number_format($kelas->total_keluar, 0, ',', '.') }}
                                </td>
                                <td data-label="Saldo Kas" class="text-xs font-bold font-heading text-right text-slate-900">
                                    Rp {{ number_format($kelas->saldo_kas, 0, ',', '.') }}
                                </td>
                                <td data-label="Aksi" class="text-center">
                                    <a href="{{ route('admin.kelas.edit', $kelas->id) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-700 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-lg transition-all shadow-sm">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                        <span>Kelola</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-10 text-slate-400 text-xs sm:text-sm">
                <span class="material-symbols-outlined text-4xl text-slate-300 mb-1">school</span>
                <p>Belum ada data kelas yang terdaftar dalam sistem.</p>
                <a href="{{ route('admin.kelas.create') }}" class="text-xs font-semibold text-[#1B4F72] hover:underline mt-2 inline-block">
                    + Tambah Kelas Pertama Sekarang
                </a>
            </div>
        @endif
    </div>

    <!-- Recent System-Wide Transactions Table -->
    <div class="card p-5 sm:p-6 border border-slate-200/80 rounded-2xl shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 font-heading">Transaksi Terbaru Seluruh Kelas</h3>
                <p class="text-xs text-slate-500 mt-0.5">Catatan transaksi kas terbaru yang dicatat oleh bendahara di semua kelas.</p>
            </div>
        </div>

        @if($recentTransactions->count() > 0)
            <div class="p-2 sm:p-5 overflow-x-visible sm:overflow-x-auto w-full">
                <table class="w-full text-left table-clean table-responsive-cards recent-admin-transactions text-xs sm:text-sm">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kelas</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>Dicatat Oleh</th>
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
                                <td data-label="Dicatat Oleh" class="text-xs text-slate-600">
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
            <div class="text-center py-10 text-slate-400 text-xs">
                <span class="material-symbols-outlined text-4xl mb-1 text-slate-300">receipt_long</span>
                <p class="text-slate-500">Belum ada transaksi kas yang dicatat di kelas manapun.</p>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    /* Responsive Mobile Card View for Admin Tables */
    @media (max-width: 767.98px) {
        .admin-class-table, 
        .admin-class-table tbody, 
        .admin-class-table tr, 
        .admin-class-table td,
        .recent-admin-transactions,
        .recent-admin-transactions tbody,
        .recent-admin-transactions tr,
        .recent-admin-transactions td {
            display: block;
            width: 100% !important;
            box-sizing: border-box;
        }
        .admin-class-table thead,
        .recent-admin-transactions thead {
            display: none;
        }
        .admin-class-table tr,
        .recent-admin-transactions tr {
            margin-bottom: 0.875rem;
            background: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }
        .admin-class-table td,
        .recent-admin-transactions td {
            padding: 0.35rem 0 !important;
            border: none !important;
            text-align: left !important;
        }
        .admin-class-table td::before,
        .recent-admin-transactions td::before {
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
        const classNames = @json($chartClassNames);
        const classBalances = @json($chartClassBalances);
        const classStudents = @json($chartClassStudents);

        // 1. Column Chart: Sebaran Saldo Kas Antar Kelas
        const balanceChartEl = document.querySelector("#adminClassBalanceChart");
        if (balanceChartEl) {
            const balanceOptions = {
                series: [{
                    name: 'Saldo Kas',
                    data: classBalances
                }],
                chart: {
                    type: 'bar',
                    height: 280,
                    toolbar: { show: false },
                    fontFamily: 'Work Sans, sans-serif'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '45%',
                        distributed: true
                    }
                },
                colors: ['#1B4F72', '#5DCAA5', '#0284C7', '#8B5CF6', '#F59E0B'],
                dataLabels: { enabled: false },
                xaxis: {
                    categories: classNames,
                    labels: {
                        style: { colors: '#64748B', fontSize: '11px', fontFamily: 'Work Sans' }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#64748B', fontSize: '11px', fontFamily: 'Work Sans' },
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
                legend: { show: false }
            };
            const balanceChart = new ApexCharts(balanceChartEl, balanceOptions);
            balanceChart.render();
        }

        // 2. Donut Chart: Distribusi Siswa per Kelas
        const studentChartEl = document.querySelector("#adminStudentDistChart");
        if (studentChartEl && classStudents.length > 0 && classStudents.some(v => v > 0)) {
            const studentOptions = {
                series: classStudents,
                labels: classNames,
                chart: {
                    type: 'donut',
                    height: 270,
                    fontFamily: 'Work Sans, sans-serif'
                },
                colors: ['#1B4F72', '#5DCAA5', '#38BDF8', '#A78BFA', '#FBBF24'],
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
                                    fontSize: '12px',
                                    color: '#64748B',
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + ' Anak';
                                    }
                                }
                            }
                        }
                    }
                }
            };
            const studentChart = new ApexCharts(studentChartEl, studentOptions);
            studentChart.render();
        } else if (studentChartEl) {
            studentChartEl.innerHTML = '<p class="text-xs text-gray-400 py-10">Belum ada data siswa terdaftar.</p>';
        }
    });
</script>
@endpush
</x-app-layout>