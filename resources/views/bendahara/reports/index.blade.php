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
                Pemisahan rekap arus kas riil (cash flow) dan status kepatuhan tunggakan kas siswa.
            </p>
        </div>

        <div id="tour-bendahara-report-actions" class="flex flex-wrap items-center gap-2.5">
            @if($activeTab === 'buku_kas')
                <a id="btnExportExcel" 
                   href="{{ route('bendahara.report.exportExcel', array_filter(['start_date' => $startDate, 'end_date' => $endDate, 'category_id' => $categoryId, 'type' => $type])) }}" 
                   title="Unduh Buku Kas Umum ke Excel (.xlsx)"
                   class="py-2.5 px-4 text-xs sm:text-sm inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold transition-all shadow-sm">
                    <span class="material-symbols-outlined text-base">table</span> Ekspor Excel
                </a>
                <a id="btnExportPdf" 
                   href="{{ route('bendahara.report.exportPdf', array_filter(['start_date' => $startDate, 'end_date' => $endDate, 'category_id' => $categoryId, 'type' => $type])) }}" 
                   target="_blank"
                   title="Cetak Buku Kas PDF (Buka di Tab Baru)"
                   class="btn-navy py-2.5 px-4 text-xs sm:text-sm inline-flex items-center justify-center gap-2 rounded-xl font-semibold shadow-sm">
                    <span class="material-symbols-outlined text-base">picture_as_pdf</span> Cetak PDF
                </a>
            @else
                <a id="btnExportExcel" 
                   href="{{ route('bendahara.students.exportExcel', ['tahun_ajaran' => $selectedTahunAjaran]) }}" 
                   title="Unduh Rekap Tunggakan Siswa ke Excel (.xlsx)"
                   class="py-2.5 px-4 text-xs sm:text-sm inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold transition-all shadow-sm">
                    <span class="material-symbols-outlined text-base">table</span> Ekspor Excel (TA {{ $selectedTahunAjaran }})
                </a>
                <a id="btnExportPdf" 
                   href="{{ route('bendahara.students.exportPdf', ['tahun_ajaran' => $selectedTahunAjaran]) }}" 
                   title="Cetak Rekap Tunggakan PDF"
                   class="btn-navy py-2.5 px-4 text-xs sm:text-sm inline-flex items-center justify-center gap-2 rounded-xl font-semibold shadow-sm">
                    <span class="material-symbols-outlined text-base">picture_as_pdf</span> Cetak PDF (TA {{ $selectedTahunAjaran }})
                </a>
            @endif
        </div>
    </div>

    <!-- 2 Dedicated Report Modules Tab Navigation -->
    <div class="flex items-center gap-2 p-1.5 bg-slate-100/90 border border-slate-200/80 rounded-2xl w-fit">
        <a href="{{ route('bendahara.report.index', ['tab' => 'buku_kas']) }}" 
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all {{ $activeTab === 'buku_kas' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/60' : 'text-slate-500 hover:text-slate-800' }}">
            <span class="material-symbols-outlined text-lg {{ $activeTab === 'buku_kas' ? 'text-teal-600' : 'text-slate-400' }}">receipt_long</span>
            <span>Tab 1: Buku Kas Umum</span>
            <span class="hidden sm:inline-block text-[10px] px-2 py-0.5 rounded-md {{ $activeTab === 'buku_kas' ? 'bg-teal-50 text-teal-700 font-semibold' : 'bg-slate-200/70 text-slate-500' }}">Arus Kas Riil</span>
        </a>
        <a href="{{ route('bendahara.report.index', ['tab' => 'tunggakan', 'tahun_ajaran' => $selectedTahunAjaran]) }}" 
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all {{ $activeTab === 'tunggakan' ? 'bg-white text-slate-900 shadow-sm border border-slate-200/60' : 'text-slate-500 hover:text-slate-800' }}">
            <span class="material-symbols-outlined text-lg {{ $activeTab === 'tunggakan' ? 'text-navy' : 'text-slate-400' }}">fact_check</span>
            <span>Tab 2: Status Tunggakan Siswa</span>
            <span class="hidden sm:inline-block text-[10px] px-2 py-0.5 rounded-md {{ $activeTab === 'tunggakan' ? 'bg-navy/10 text-navy font-semibold' : 'bg-slate-200/70 text-slate-500' }}">Piutang & Target</span>
        </a>
    </div>

    @if($activeTab === 'buku_kas')
        <!-- ============================================================== -->
        <!-- TAB 1: BUKU KAS UMUM (ARUS KAS RIIL BERDASARKAN TANGGAL)       -->
        <!-- ============================================================== -->

        <!-- Filter Card -->
        <div id="tour-bendahara-report-filter" class="card p-5 sm:p-6 border border-slate-200/80 rounded-2xl shadow-sm">
            <form method="GET" action="{{ route('bendahara.report.index') }}" id="filterForm" class="space-y-4">
                <input type="hidden" name="tab" value="buku_kas">
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
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kategori Transaksi</label>
                        <select name="category_id" class="input-clean w-full px-3 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-200 rounded-xl">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }} ({{ ucfirst($cat->tipe) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 btn-navy py-2.5 px-4 text-xs sm:text-sm font-semibold rounded-xl inline-flex items-center justify-center gap-1.5">
                            <span class="material-symbols-outlined text-base">filter_alt</span> Terapkan Filter
                        </button>
                        <a href="{{ route('bendahara.report.index', ['tab' => 'buku_kas']) }}" class="p-2.5 text-slate-400 hover:text-slate-700 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors" title="Reset Filter">
                            <span class="material-symbols-outlined text-base">refresh</span>
                        </a>
                    </div>
                </div>

                <!-- Quick Filter Presets -->
                <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-slate-100 text-xs">
                    <span class="text-slate-400 text-xs font-medium">Filter Cepat:</span>
                    <button type="button" onclick="setPreset('this_month')" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition-colors text-xs">
                        Bulan Ini
                    </button>
                    <button type="button" onclick="setPreset('last_month')" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition-colors text-xs">
                        Bulan Lalu
                    </button>
                    <button type="button" onclick="setPreset('this_year')" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium transition-colors text-xs">
                        Tahun Ini
                    </button>
                </div>
            </form>
        </div>

        <!-- 4 Summary Metric Cards (Cash Flow) -->
        <div id="tour-bendahara-report-stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Saldo Awal -->
            <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500">Saldo Awal Fisik</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-slate-800 font-heading mt-1">
                        Rp {{ number_format($saldoAwal, 0, ',', '.') }}
                    </p>
                    <span class="text-[11px] text-slate-400 mt-1 block">Sebelum {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">history</span>
                </div>
            </div>

            <!-- Total Pemasukan -->
            <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Pemasukan Riil</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-emerald-600 font-heading mt-1">
                        +Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </p>
                    <span class="text-[11px] text-emerald-600/80 font-medium mt-1 block">Total dana masuk periode ini</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">arrow_downward</span>
                </div>
            </div>

            <!-- Total Pengeluaran -->
            <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Pengeluaran Riil</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-rose-600 font-heading mt-1">
                        -Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </p>
                    <span class="text-[11px] text-rose-500/80 font-medium mt-1 block">Total dana keluar periode ini</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">arrow_upward</span>
                </div>
            </div>

            <!-- Saldo Akhir -->
            <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500">Saldo Kas Akhir Fisik</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading mt-1">
                        Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                    </p>
                    <span class="text-[11px] text-slate-400 mt-1 block">Per {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-[#1B4F72] text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-xl">account_balance_wallet</span>
                </div>
            </div>
        </div>

        <!-- Cash Flow Trend Chart Card -->
        <div id="tour-bendahara-report-chart" class="card p-5 sm:p-6 border border-slate-200/80 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900 font-heading">Grafik Arus Kas Bulanan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Perbandingan total pemasukan dan pengeluaran kas kelas setiap bulan.</p>
                </div>
                <span class="text-xs text-slate-500 bg-slate-100 px-3 py-1 rounded-full font-medium hidden sm:inline-flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Tren Kas
                </span>
            </div>
            <div id="cashFlowChart" style="min-height: 280px;"></div>
        </div>

        <!-- Ledger Table Card -->
        <div id="tour-bendahara-report-table" class="card overflow-hidden border border-slate-200/80 rounded-2xl shadow-sm">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="text-base font-bold text-slate-900 font-heading">Buku Mutasi Kas Umum</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar transaksi kas beserta saldo berjalan secara kronologis (Cash-Basis).</p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-700">
                    Total: {{ $transactions->count() }} Transaksi
                </span>
            </div>

            <div class="p-2 sm:p-5 overflow-x-visible sm:overflow-x-auto w-full">
                <table id="reportTable" class="w-full table-clean table-responsive-cards text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">No</th>
                            <th class="w-28">Tanggal</th>
                            <th class="w-32">Kategori</th>
                            <th class="min-w-[180px]">Keterangan</th>
                            <th class="w-28">Tipe</th>
                            <th class="text-right w-32">Jumlah</th>
                            <th class="text-right w-36">Saldo Berjalan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $index => $tx)
                            @php
                                $isIncome = $tx->jenis_transaksi === 'pemasukan';
                            @endphp
                            <tr>
                                <td data-label="No" class="text-center font-medium text-slate-400">{{ $index + 1 }}</td>
                                <td data-label="Tanggal" class="font-medium text-slate-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($tx->tanggal_transaksi)->format('d M Y') }}
                                </td>
                                <td data-label="Kategori">
                                    @php
                                        $catName = $tx->kategori->nama_kategori ?? '-';
                                    @endphp
                                    @if($catName === 'Uang Kas')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-teal-50 text-teal-700 border border-teal-200/60 inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>{{ $catName }}
                                        </span>
                                    @elseif($isIncome)
                                        <span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $catName }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-slate-100 text-slate-700 border border-slate-200/60 inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>{{ $catName }}
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Keterangan">
                                    @php
                                        $studentName = $tx->detailTransaksi->first()?->siswa?->nama;
                                    @endphp
                                    @if($studentName)
                                        <div>
                                            <span class="font-medium text-slate-800">{{ $tx->keterangan ?? '-' }}</span>
                                            <br>
                                            <span class="text-[11px] text-slate-400 inline-flex items-center gap-1 mt-0.5">
                                                <span class="material-symbols-outlined text-xs">person</span>{{ $studentName }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="font-medium text-slate-800">{{ $tx->keterangan ?? '-' }}</span>
                                    @endif
                                </td>
                                <td data-label="Tipe">
                                    @if($isIncome)
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">arrow_downward</span>Pemasukan
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-rose-50 text-rose-600 border border-rose-200/60 inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">arrow_upward</span>Pengeluaran
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Jumlah" class="text-right font-semibold whitespace-nowrap {{ $isIncome ? 'text-emerald-600' : 'text-rose-600' }}">
                                    Rp {{ number_format($tx->total_nominal, 0, ',', '.') }}
                                </td>
                                <td data-label="Saldo Saat Ini" class="text-right font-bold text-slate-900 whitespace-nowrap font-heading">
                                    Rp {{ number_format($tx->running_balance, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">receipt_long</span>
                                    <p class="text-sm font-medium text-slate-600">Tidak ada catatan transaksi kas</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Belum ada transaksi pada rentang tanggal yang dipilih.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <!-- ============================================================== -->
        <!-- TAB 2: STATUS TUNGGAKAN SISWA (PIUTANG & TARGET TAHUN AJARAN)  -->
        <!-- ============================================================== -->

        <!-- Filter Card for Academic Year -->
        <div class="card p-5 sm:p-6 border border-slate-200/80 rounded-2xl shadow-sm">
            <form method="GET" action="{{ route('bendahara.report.index') }}" id="filterTungggakanForm" class="space-y-4">
                <input type="hidden" name="tab" value="tunggakan">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div class="w-full sm:w-72">
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-navy text-sm">calendar_month</span>
                            Pilih Tahun Ajaran
                        </label>
                        <select name="tahun_ajaran" onchange="this.form.submit()" class="input-clean w-full px-3.5 py-2.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-200 rounded-xl">
                            @foreach($tahunAjaranList as $ta)
                                <option value="{{ $ta }}" {{ $selectedTahunAjaran === $ta ? 'selected' : '' }}>
                                    Tahun Ajaran {{ $ta }} {{ $ta === ($kelas->tahun_ajaran ?? '2025/2026') ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600">
                            <span class="font-medium text-slate-500">Parameter Tagihan:</span>
                            <span class="font-bold text-slate-800 ml-1">
                                {{ count($periods) }} Slot x Rp {{ number_format($feeAmount, 0, ',', '.') }} = Rp {{ number_format(count($periods) * $feeAmount, 0, ',', '.') }} / siswa
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- 4 Summary Metric Cards (Tunggakan & Piutang) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Target Kas -->
            <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Target Tagihan Kas</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading mt-1">
                        Rp {{ number_format($totalTargetKas, 0, ',', '.') }}
                    </p>
                    <span class="text-[11px] text-slate-400 mt-1 block">{{ $totalSiswa }} Siswa · TA {{ $selectedTahunAjaran }}</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">flag</span>
                </div>
            </div>

            <!-- Kas Terkumpul -->
            <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500">Kas Terkumpul (Lunas)</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-emerald-600 font-heading mt-1">
                        Rp {{ number_format($totalKasTerkumpul, 0, ',', '.') }}
                    </p>
                    <span class="text-[11px] text-emerald-600/90 font-semibold mt-1 block">{{ $persentaseLunas }}% dari target tercapai</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">payments</span>
                </div>
            </div>

            <!-- Sisa Piutang / Tunggakan -->
            <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500">Total Sisa Tunggakan</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-rose-600 font-heading mt-1">
                        Rp {{ number_format($totalSisaTunggakan, 0, ',', '.') }}
                    </p>
                    <span class="text-[11px] text-rose-500/80 font-medium mt-1 block">Belum dibayar oleh siswa</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">pending_actions</span>
                </div>
            </div>

            <!-- Kepatuhan Siswa -->
            <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500">Kepatuhan Bayar Siswa</p>
                    <p class="text-xl sm:text-2xl font-extrabold text-navy font-heading mt-1">
                        {{ $countLunas }} Lunas
                    </p>
                    <span class="text-[11px] text-slate-500 font-medium mt-1 block">
                        {{ $countSebagian }} Sebagian · {{ $countBelum }} Belum Bayar
                    </span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-navy/10 text-navy flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">check_circle</span>
                </div>
            </div>
        </div>

        <!-- Progress Bar Card -->
        <div class="card p-5 sm:p-6 border border-slate-200/80 rounded-2xl shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2.5">
                <div>
                    <h4 class="text-sm font-bold text-slate-800 font-heading">Tingkat Pengumpulan Kas Tahun Ajaran {{ $selectedTahunAjaran }}</h4>
                    <p class="text-xs text-slate-500">Persentase dana kas siswa yang berhasil dihimpun dari seluruh tagihan periode aktif.</p>
                </div>
                <span class="text-base font-extrabold text-emerald-600 font-heading">{{ $persentaseLunas }}%</span>
            </div>
            <div class="w-full h-3.5 bg-slate-100 rounded-full overflow-hidden p-0.5 border border-slate-200/70">
                <div class="h-full bg-gradient-to-r from-teal-500 to-emerald-500 rounded-full transition-all duration-500" style="width: {{ min(100, $persentaseLunas) }}%"></div>
            </div>
        </div>

        <!-- Student Arrears Table Card -->
        <div class="card overflow-hidden border border-slate-200/80 rounded-2xl shadow-sm">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="text-base font-bold text-slate-900 font-heading">Daftar Status Tunggakan Per Siswa</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Rincian kewajiban kas, nominal terbayar, dan sisa tunggakan per siswa pada TA {{ $selectedTahunAjaran }}.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('bendahara.students.index', ['tahun_ajaran' => $selectedTahunAjaran]) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">checklist</span> Buka Matriks Checklist
                    </a>
                </div>
            </div>

            <div class="p-2 sm:p-5 overflow-x-visible sm:overflow-x-auto w-full">
                <table id="tunggakanTable" class="w-full table-clean table-responsive-cards text-left border-collapse text-xs sm:text-sm">
                    <thead>
                        <tr>
                            <th class="w-10 text-center">No</th>
                            <th class="min-w-[180px]">Nama Siswa</th>
                            <th class="w-28">NIS</th>
                            <th class="text-right w-28">Target (Rp)</th>
                            <th class="text-right w-32">Dibayar (Rp)</th>
                            <th class="text-right w-32">Sisa Tunggakan (Rp)</th>
                            <th class="w-28 text-center">Slot Terbayar</th>
                            <th class="w-28 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($studentsArrears as $index => $stu)
                            <tr>
                                <td data-label="No" class="text-center font-medium text-slate-400">{{ $index + 1 }}</td>
                                <td data-label="Nama Siswa">
                                    <div class="font-semibold text-slate-900">{{ $stu->nama }}</div>
                                    @if($stu->no_hp)
                                        <div class="text-[11px] text-slate-400">{{ $stu->no_hp }}</div>
                                    @endif
                                </td>
                                <td data-label="NIS" class="font-mono text-slate-600">{{ $stu->nis ?? '-' }}</td>
                                <td data-label="Target" class="text-right font-medium text-slate-600">
                                    Rp {{ number_format($stu->target_nominal, 0, ',', '.') }}
                                </td>
                                <td data-label="Dibayar" class="text-right font-semibold text-emerald-600">
                                    Rp {{ number_format($stu->total_dibayar, 0, ',', '.') }}
                                </td>
                                <td data-label="Sisa Tunggakan" class="text-right font-bold font-heading {{ $stu->total_tunggakan > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                    Rp {{ number_format($stu->total_tunggakan, 0, ',', '.') }}
                                </td>
                                <td data-label="Slot Terbayar" class="text-center">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                        {{ $stu->slots_paid }} / {{ count($periods) }}
                                    </span>
                                </td>
                                <td data-label="Status" class="text-center">
                                    @if($stu->status_bayar === 'Lunas')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">check_circle</span> Lunas
                                        </span>
                                    @elseif($stu->status_bayar === 'Sebagian')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-amber-50 text-amber-700 border border-amber-200/60 inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">hourglass_top</span> Sebagian
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-rose-50 text-rose-600 border border-rose-200/60 inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">cancel</span> Belum Bayar
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12 text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">group_off</span>
                                    <p class="text-sm font-medium text-slate-600">Belum ada data siswa</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Siswa belum ditambahkan ke kelas ini.</p>
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
    @if($activeTab === 'buku_kas')
        if ($('#reportTable tbody tr').length > 1 || !$('#reportTable tbody td[colspan]').length) {
            $('#reportTable').DataTable({
                pageLength: 20,
                ordering: false,
                autoWidth: false,
                dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4 gap-3"lf>rt<"flex flex-col sm:flex-row justify-between items-center mt-4 gap-3"ip>',
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari transaksi...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ transaksi",
                    infoEmpty: "Tidak ada data",
                    zeroRecords: "Tidak ada transaksi yang cocok",
                    paginate: { previous: "←", next: "→" }
                }
            });
        }

        // Render ApexCharts for Cash Flow Trend
        const months = @json($months ?? []);
        const incomeData = @json($incomeData ?? []);
        const expenseData = @json($expenseData ?? []);

        if (document.querySelector("#cashFlowChart") && months.length > 0) {
            const chartOptions = {
                series: [
                    { name: 'Pemasukan Kas', data: incomeData },
                    { name: 'Pengeluaran Kas', data: expenseData }
                ],
                chart: {
                    type: 'area',
                    height: 280,
                    fontFamily: 'Work Sans, sans-serif',
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                colors: ['#5DCAA5', '#E11D48'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [0, 95, 100]
                    }
                },
                stroke: { curve: 'smooth', width: 2.5 },
                dataLabels: { enabled: false },
                grid: {
                    borderColor: '#F1F5F9',
                    strokeDashArray: 4
                },
                xaxis: {
                    categories: months,
                    labels: {
                        style: { colors: '#94A3B8', fontSize: '11px', fontWeight: 500 }
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#94A3B8', fontSize: '11px' },
                        formatter: function (val) {
                            if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
                            if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'k';
                            return 'Rp ' + val;
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '12px',
                    markers: { radius: 12 }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return 'Rp ' + Number(val).toLocaleString('id-ID');
                        }
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#cashFlowChart"), chartOptions);
            chart.render();
        }
    @else
        if ($('#tunggakanTable tbody tr').length > 1 || !$('#tunggakanTable tbody td[colspan]').length) {
            $('#tunggakanTable').DataTable({
                pageLength: 20,
                ordering: true,
                autoWidth: false,
                order: [[5, 'desc']], // Default order by sisa tunggakan descending
                dom: '<"flex flex-col sm:flex-row justify-between items-center mb-4 gap-3"lf>rt<"flex flex-col sm:flex-row justify-between items-center mt-4 gap-3"ip>',
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari nama / NIS siswa...",
                    lengthMenu: "Tampilkan _MENU_ siswa",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ siswa",
                    infoEmpty: "Tidak ada data siswa",
                    zeroRecords: "Tidak ada siswa yang cocok",
                    paginate: { previous: "←", next: "→" }
                }
            });
        }
    @endif
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

    const formatDate = (d) => {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    if (start && end) {
        const startVal = formatDate(start);
        const endVal = formatDate(end);
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
}
</script>
</x-app-layout>
