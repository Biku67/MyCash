<x-app-layout>
@section('page-title', 'Detail Pengumuman Kas')

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back Navigation & Top Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <a href="{{ route('bendahara.announcements.index') }}" class="text-slate-500 hover:text-slate-800 text-xs sm:text-sm inline-flex items-center gap-1.5 transition-colors font-medium">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali ke Daftar Pengumuman
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('bendahara.announcements.edit', $announcement->id) }}" 
               class="py-2.5 px-3.5 text-xs sm:text-sm font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl inline-flex items-center gap-1.5 shadow-sm transition-all">
                <span class="material-symbols-outlined text-base">edit</span>
                <span>Edit Pengumuman</span>
            </a>
            <form method="POST" action="{{ route('bendahara.announcements.destroy', $announcement->id) }}" class="m-0"
                  onsubmit="return swalConfirm(event, 'Hapus Pengumuman?', 'Pengumuman ini beserta riwayat status keterbacaan siswa akan dihapus permanen.')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="py-2.5 px-3.5 text-xs sm:text-sm font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-100 rounded-xl inline-flex items-center gap-1.5 transition-all">
                    <span class="material-symbols-outlined text-base">delete</span>
                    <span>Hapus</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Announcement Content Card -->
    <div class="card p-6 sm:p-8 border border-slate-200/80 rounded-2xl shadow-sm">
        <div class="flex flex-wrap items-center gap-2 mb-3">
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                Kelas {{ $announcement->kelas ? $announcement->kelas->nama_kelas : '-' }}
            </span>
            @if(isset($isAllClass) && $isAllClass)
                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200/60 inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">groups</span>
                    Satu Kelas (Semua Siswa)
                </span>
            @else
                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200/60 inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">person</span>
                    Perorangan ({{ $totalRecipients }} Siswa)
                </span>
            @endif
            <span class="text-xs text-slate-400 flex items-center gap-1">
                <span class="material-symbols-outlined text-xs">schedule</span>
                Dikirim: {{ \Carbon\Carbon::parse($announcement->created_at)->translatedFormat('l, d F Y - H:i') }} WIB
            </span>
        </div>

        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-heading tracking-tight mb-4">
            {{ $announcement->judul }}
        </h1>

        <div class="p-5 bg-slate-50/80 rounded-2xl border border-slate-200/80 text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line font-normal">
            {{ $announcement->isi }}
        </div>
    </div>

    <!-- Read Statistics Breakdown -->
    @php
        $pct = $totalRecipients > 0 ? round(($readCount / $totalRecipients) * 100) : 0;
        $unreadCount = max(0, $totalRecipients - $readCount);
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Sudah Membaca -->
        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">done_all</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Sudah Membaca</p>
                <div class="flex items-baseline gap-2">
                    <p class="text-xl font-extrabold text-emerald-600 font-heading">{{ $readCount }}</p>
                    <span class="text-xs font-semibold text-slate-400">/ {{ $totalRecipients }} Siswa ({{ $pct }}%)</span>
                </div>
            </div>
        </div>

        <!-- Belum Membaca -->
        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">mark_email_unread</span>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Belum Membaca</p>
                <p class="text-xl font-extrabold text-amber-600 font-heading">{{ $unreadCount }} Siswa</p>
            </div>
        </div>

        <!-- Progress Visual -->
        <div class="card p-5 border border-slate-200/80 rounded-2xl shadow-sm flex flex-col justify-center">
            <div class="flex items-center justify-between text-xs mb-2">
                <span class="font-medium text-slate-500">Tingkat Keterbacaan</span>
                <span class="font-bold text-slate-900 font-heading">{{ $pct }}%</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
            </div>
        </div>
    </div>

    <!-- Student List Table Card -->
    <div class="card overflow-hidden border border-slate-200/80 rounded-2xl shadow-sm">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 font-heading">Daftar Status Siswa</h3>
                <p class="text-xs text-slate-500 mt-0.5">Rincian status keterbacaan setiap siswa di kelas ini.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                {{ $totalRecipients }} Siswa
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-clean text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr>
                        <th class="w-12 text-center">No</th>
                        <th>Nama Siswa</th>
                        <th class="w-32">NIS</th>
                        <th class="w-48 text-center">Status</th>
                        <th class="w-48 text-center">Waktu Dibaca</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recipients as $index => $item)
                        <tr class="hover:bg-canvas/50 transition-colors">
                            <td class="text-center font-mono text-gray-400">{{ $index + 1 }}</td>
                            <td>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-teal-accent/20 text-teal-accent flex items-center justify-center font-bold text-xs">
                                        {{ substr($item->siswa->nama ?? 'S', 0, 1) }}
                                    </div>
                                    <span class="font-medium text-navy">{{ $item->siswa->nama ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="font-mono text-gray-500">{{ $item->siswa->nis ?? '-' }}</td>
                            <td class="text-center">
                                @if($item->is_read)
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">check_circle</span>
                                        Sudah Dibaca
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-gray-100 text-gray-500 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">hourglass_empty</span>
                                        Belum Dibaca
                                    </span>
                                @endif
                            </td>
                            <td class="text-center text-gray-500 text-xs">
                                @if($item->is_read && $item->read_at)
                                    {{ \Carbon\Carbon::parse($item->read_at)->translatedFormat('d M Y, H:i') }} WIB
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400 text-xs">
                                Tidak ada data siswa penerima.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</x-app-layout>
