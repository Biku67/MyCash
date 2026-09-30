<x-app-layout>
@section('page-title', 'Dashboard Siswa')

<div class="space-y-6">
    <!-- Header Welcome Card -->
    <div class="card p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-heading">
                Selamat Datang! <span class="text-navy">{{ $student ? $student->nama : Auth::user()->name }}</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1 leading-relaxed">Pantau riwayat setoran iuran dan pengumuman kas kelas Anda.</p>
        </div>
        <div>
            <span class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm text-teal-accent">school</span>
                Kelas {{ $kelas ? $kelas->nama_kelas : 'Siswa MyCash' }}
            </span>
        </div>
    </div>

    <!-- Latest Announcement Banner (if any) -->
    @if(isset($latestNotification) && $latestNotification->pengumuman)
        @php
            $ann = $latestNotification->pengumuman;
            $isUnread = !$latestNotification->is_read;
            $isPaymentNotif = str_contains($ann->judul, 'Pembayaran Kas');
        @endphp
        <div class="card p-5 border {{ $isUnread ? ($isPaymentNotif ? 'border-emerald-300 bg-emerald-50/20' : 'border-teal-300 bg-teal-50/20') : 'border-slate-200' }} flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-start gap-3.5 min-w-0">
                <div class="w-10 h-10 rounded-xl {{ $isUnread ? ($isPaymentNotif ? 'bg-emerald-600 text-white' : 'bg-teal-accent text-white') : ($isPaymentNotif ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500') }} flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-xl">{{ $isPaymentNotif ? 'payments' : 'campaign' }}</span>
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        @if($isUnread)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500 text-white uppercase tracking-wider">
                                Baru
                            </span>
                        @endif
                        <span class="text-xs font-semibold {{ $isPaymentNotif ? 'text-emerald-700' : 'text-teal-700' }}">
                            {{ $isPaymentNotif ? 'Pembayaran Kas' : 'Pengumuman Kelas' }}
                        </span>
                        <span class="text-xs text-slate-400">&bull; {{ \Carbon\Carbon::parse($ann->created_at)->translatedFormat('d M Y, H:i') }} WIB</span>
                    </div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 font-heading truncate">
                        {{ $ann->judul }}
                    </h2>
                    <p class="text-xs text-slate-600 mt-0.5 line-clamp-1 leading-relaxed">
                        {{ \Illuminate\Support\Str::limit(strip_tags($ann->isi), 120) }}
                    </p>
                </div>
            </div>
            <a href="{{ route('siswa.notifications.show', $latestNotification->id) }}" 
               class="py-2.5 px-4 text-xs sm:text-sm font-semibold {{ $isUnread ? 'bg-teal-accent hover:bg-teal-accent-dark text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }} rounded-xl inline-flex items-center justify-center gap-1.5 transition-all flex-shrink-0">
                <span>Baca Selengkapnya</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
    @endif

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Status Iuran Siswa -->
        <div id="tour-siswa-stat-card" class="card p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">account_balance_wallet</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Total Kas Disetor</p>
                <p class="text-xl font-extrabold text-emerald-600 font-heading mt-0.5">
                    Rp {{ number_format($totalPaid, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Kelas Siswa -->
        <div class="card p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">school</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Kelas</p>
                <p class="text-xl font-bold text-slate-900 font-heading mt-0.5">{{ $kelas ? $kelas->nama_kelas : '-' }}</p>
            </div>
        </div>

        <!-- NIS -->
        <div class="card p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">badge</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Nomor Induk Siswa (NIS)</p>
                <p class="text-xl font-bold text-slate-900 font-heading font-mono mt-0.5">{{ $student ? ($student->nis ?? '-') : '-' }}</p>
            </div>
        </div>
    </div>

    <!-- Action to Checklist Riwayat Pembayaran -->
    <div class="card p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-start gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                <span class="material-symbols-outlined text-2xl">checklist</span>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900 font-heading">Riwayat & Checklist Pembayaran Kas</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5 leading-relaxed">
                    Lihat status centang iuran kas Anda per periode serta rincian setoran yang telah dicatat oleh bendahara kelas.
                </p>
            </div>
        </div>
        <a href="{{ route('siswa.history.index') }}" class="btn-navy py-2.5 px-5 text-xs sm:text-sm inline-flex items-center justify-center gap-2 flex-shrink-0 transition-all">
            <span class="material-symbols-outlined text-base">fact_check</span>
            Buka Riwayat Kas
        </a>
    </div>

    <!-- Notice Card -->
    <div class="card p-5">
        <div class="flex items-center gap-2.5 mb-2">
            <span class="material-symbols-outlined text-slate-500 text-lg">info</span>
            <h4 class="text-sm font-bold text-slate-800 font-heading">Informasi Penyetoran</h4>
        </div>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
            Setorkan uang kas langsung kepada bendahara kelas Anda. Setelah dicatat, status pembayaran Anda akan langsung tercentang secara otomatis di halaman riwayat kas.
        </p>
    </div>
</div>
</x-app-layout>