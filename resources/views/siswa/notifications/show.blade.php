<x-app-layout>
@section('page-title', 'Detail Pengumuman Kas')

@php
    $announcement = $recipient->pengumuman;
    $isPaymentNotif = $announcement && str_contains($announcement->judul, 'Pembayaran Kas');
@endphp

<div class="max-w-3xl mx-auto space-y-5">
    <!-- Back Navigation -->
    <div>
        <a href="{{ route('siswa.notifications.index') }}" class="text-slate-500 hover:text-slate-900 text-xs sm:text-sm inline-flex items-center gap-1.5 transition-colors font-medium">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            <span>Kembali ke Daftar Notifikasi</span>
        </a>
    </div>

    <!-- Announcement Detail Card -->
    <div class="card p-6 sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5 pb-4 border-b border-slate-100">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">
                    Kelas {{ $announcement->kelas ? $announcement->kelas->nama_kelas : '-' }}
                </span>
                @if($isPaymentNotif)
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">payments</span>Pembayaran Kas
                    </span>
                @endif
                <span class="text-xs text-slate-400 flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">schedule</span>
                    {{ \Carbon\Carbon::parse($announcement->created_at)->translatedFormat('l, d F Y - H:i') }} WIB
                </span>
            </div>
            <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-xs">check_circle</span>
                Telah Dibaca
            </span>
        </div>

        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mb-2 font-heading">
            {{ $announcement->judul }}
        </h1>

        <p class="text-xs text-slate-500 mb-6 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm text-slate-400">person</span>
            Oleh <span class="font-semibold text-slate-700">{{ $announcement->bendahara->user->name ?? 'Bendahara Kelas' }}</span>
        </p>

        <!-- Announcement Body -->
        <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl border border-slate-200/60 text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line font-normal mb-6">
            {{ $announcement->isi }}
        </div>

        <!-- Quick Help & Kas Check Callout -->
        <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-slate-600 text-xl">fact_check</span>
                <p class="text-xs text-slate-600">
                    Ingin memeriksa status checklist kas pribadi Anda?
                </p>
            </div>
            <a href="{{ route('siswa.history.index') }}" class="btn-navy py-2 px-4 text-xs font-semibold inline-flex items-center justify-center gap-1.5 rounded-lg flex-shrink-0">
                <span>Cek Riwayat Kas</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
    </div>
</div>

</x-app-layout>
