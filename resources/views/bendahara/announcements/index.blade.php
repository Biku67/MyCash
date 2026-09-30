<x-app-layout>
@section('page-title', 'Pengumuman & Notifikasi')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-heading">Pengumuman & Notifikasi Kelas</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
            Kelola pengumuman kelas serta pantau riwayat notifikasi pembayaran kas siswa kelas <span class="font-semibold text-slate-700">{{ $kelas ? $kelas->nama_kelas : 'Anda' }}</span>.
        </p>
    </div>
    <div class="flex items-center gap-2.5">
        <a id="tour-bendahara-btn-create-announcement" href="{{ route('bendahara.announcements.create') }}" class="btn-navy py-2.5 px-4 text-xs sm:text-sm inline-flex items-center justify-center gap-2 rounded-xl transition-all">
            <span class="material-symbols-outlined text-base">campaign</span>
            <span>Buat Pengumuman</span>
        </a>
    </div>
</div>

<!-- Summary Stats Bar -->
<div id="tour-bendahara-announcement-stats" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
    <div class="card p-4 flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-xl">groups</span>
        </div>
        <div>
            <p class="text-[11px] text-slate-400 font-medium">Jumlah Siswa</p>
            <p class="text-sm font-bold text-slate-900 font-heading">{{ $totalStudents }} Siswa Aktif</p>
        </div>
    </div>
    <div class="card p-4 flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-xl">campaign</span>
        </div>
        <div>
            <p class="text-[11px] text-slate-400 font-medium">Pengumuman Kelas</p>
            <p class="text-sm font-bold text-slate-900 font-heading">{{ $countPengumuman }} Pengumuman</p>
        </div>
    </div>
    <div class="card p-4 flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-xl">notifications</span>
        </div>
        <div>
            <p class="text-[11px] text-slate-400 font-medium">Notifikasi Kas Siswa</p>
            <p class="text-sm font-bold text-slate-900 font-heading">{{ $countPembayaran }} Notifikasi</p>
        </div>
    </div>
</div>

<!-- Filters & Search Toolbar -->
<div class="card p-4 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <!-- Type Filter Tabs -->
    <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl overflow-x-auto">
        <a href="{{ route('bendahara.announcements.index', ['type' => 'all', 'search' => $search]) }}"
           class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap inline-flex items-center gap-1.5 {{ $type === 'all' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900' }}">
            <span>Semua</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $type === 'all' ? 'bg-slate-200 text-slate-800' : 'bg-slate-200 text-slate-600' }}">{{ $countAll }}</span>
        </a>
        <a href="{{ route('bendahara.announcements.index', ['type' => 'pengumuman', 'search' => $search]) }}"
           class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap inline-flex items-center gap-1.5 {{ $type === 'pengumuman' ? 'bg-white text-blue-700 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900' }}">
            <span class="material-symbols-outlined text-xs">campaign</span>
            <span>Pengumuman Kelas</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $type === 'pengumuman' ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-600' }}">{{ $countPengumuman }}</span>
        </a>
        <a href="{{ route('bendahara.announcements.index', ['type' => 'pembayaran', 'search' => $search]) }}"
           class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap inline-flex items-center gap-1.5 {{ $type === 'pembayaran' ? 'bg-white text-emerald-700 shadow-xs font-bold' : 'text-slate-600 hover:text-emerald-700' }}">
            <span class="material-symbols-outlined text-xs">payments</span>
            <span>Notifikasi Kas Siswa</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $type === 'pembayaran' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">{{ $countPembayaran }}</span>
        </a>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('bendahara.announcements.index') }}" class="flex items-center gap-2 m-0 w-full md:w-auto">
        <input type="hidden" name="type" value="{{ $type }}">
        <div class="relative flex-1 md:w-64">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
            <input type="text" name="search" value="{{ $search }}" 
                   placeholder="Cari judul atau siswa..."
                   class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:border-navy focus:ring-1 focus:ring-navy placeholder-slate-400">
        </div>
        <button type="submit" class="py-2 px-3.5 bg-navy hover:bg-navy-dark text-white text-xs font-semibold rounded-xl transition-colors">
            Cari
        </button>
        @if($search || $type !== 'all')
            <a href="{{ route('bendahara.announcements.index') }}" class="py-2 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs rounded-xl transition-colors" title="Reset Filter">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
            </a>
        @endif
    </form>
</div>

<!-- List of Announcements & Payment Notifications -->
@if($announcements->isEmpty())
    <div id="tour-bendahara-announcement-list" class="card p-8 sm:p-12 border border-gray-100 shadow-sm text-center">
        <div class="w-16 h-16 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center mx-auto mb-3.5">
            <span class="material-symbols-outlined text-3xl">{{ $type === 'pembayaran' ? 'payments' : 'campaign' }}</span>
        </div>
        <h3 class="text-base sm:text-lg font-bold text-navy mb-1" style="font-family:'Manrope',sans-serif;">
            @if($search)
                Tidak Ada Hasil Pencarian
            @elseif($type === 'pembayaran')
                Belum Ada Notifikasi Pembayaran Kas
            @elseif($type === 'pengumuman')
                Belum Ada Pengumuman Kelas
            @else
                Belum Ada Pengumuman atau Notifikasi
            @endif
        </h3>
        <p class="text-xs sm:text-sm text-gray-500 max-w-md mx-auto mb-5 leading-relaxed">
            @if($search)
                Tidak ditemukan data dengan kata kunci "{{ $search }}". Silakan coba kata kunci lain.
            @elseif($type === 'pembayaran')
                Notifikasi pembayaran kas otomatis dibuat saat Anda mencatat transaksi kas siswa.
            @else
                Belum ada data untuk kelas ini. Anda bisa membuat pengumuman untuk mengingatkan iuran kas atau kegiatan kelas.
            @endif
        </p>
        @if($search || $type !== 'all')
            <a href="{{ route('bendahara.announcements.index') }}" class="btn-navy py-2 px-4 text-xs font-semibold inline-flex items-center gap-1.5 rounded-xl shadow-sm">
                <span class="material-symbols-outlined text-sm">restart_alt</span>
                <span>Tampilkan Semua</span>
            </a>
        @else
            <a href="{{ route('bendahara.announcements.create') }}" class="btn-navy py-2.5 px-5 text-xs sm:text-sm inline-flex items-center justify-center gap-2 shadow-sm hover:shadow transition-all">
                <span class="material-symbols-outlined text-base">add</span>
                <span>Buat Pengumuman Pertama</span>
            </a>
        @endif
    </div>
@else
    <div id="tour-bendahara-announcement-list" class="space-y-3.5">
        @foreach($announcements as $item)
            @php
                $isPaymentNotif = str_contains($item->judul, 'Pembayaran Kas');
                $pct = $item->total_penerima > 0 ? round(($item->dibaca_penerima / $item->total_penerima) * 100) : 0;
                $unread = max(0, $item->total_penerima - $item->dibaca_penerima);
                $singleRecipient = $item->penerima->first();
                $singleStudent = $singleRecipient?->siswa;
            @endphp

            @if($isPaymentNotif)
                {{-- ─── SIMPLIFIED COMPACT CARD UNTUK NOTIFIKASI PEMBAYARAN KAS ─── --}}
                <div class="card p-4 sm:p-5 border border-emerald-100 bg-white hover:border-emerald-300 shadow-sm transition-all">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-xl">payments</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">payments</span>
                                        Notifikasi Kas
                                    </span>
                                    @if($singleStudent)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-navy/5 text-navy inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">person</span>
                                            {{ $singleStudent->nama }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-400 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">schedule</span>
                                        {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y, H:i') }} WIB
                                    </span>
                                </div>
                                <h2 class="text-sm sm:text-base font-bold text-navy hover:text-emerald-700 transition-colors" style="font-family:'Manrope',sans-serif;">
                                    <a href="{{ route('bendahara.announcements.show', $item->id) }}">
                                        {{ $item->judul }}
                                    </a>
                                </h2>
                                <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($item->isi), 140) }}
                                </p>
                            </div>
                        </div>

                        <!-- Read Status & Action -->
                        <div class="flex items-center gap-2 self-end sm:self-center flex-shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-gray-100 w-full sm:w-auto justify-between sm:justify-end">
                            <div>
                                @if($item->dibaca_penerima > 0)
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">done_all</span>
                                        <span>Sudah Dibaca</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200/60 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">schedule</span>
                                        <span>Belum Dibaca</span>
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('bendahara.announcements.show', $item->id) }}" 
                                   class="py-1.5 px-3 text-xs font-semibold text-navy bg-navy/5 hover:bg-navy/10 rounded-lg inline-flex items-center gap-1 transition-colors"
                                   title="Rincian Bukti Notifikasi">
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                    <span>Detail</span>
                                </a>
                                <form method="POST" action="{{ route('bendahara.announcements.destroy', $item->id) }}" class="m-0"
                                      onsubmit="return swalConfirm(event, 'Hapus Notifikasi Kas?', 'Notifikasi pembayaran kas ini akan dihapus dari riwayat.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                            title="Hapus Notifikasi">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- ─── STANDARD CARD UNTUK PENGUMUMAN KELAS MANUAL ─── --}}
                <div class="card p-5 sm:p-6 border border-gray-100 shadow-sm hover:border-teal-accent/40 transition-all">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-navy/10 text-navy">
                                    Kelas {{ $kelas ? $kelas->nama_kelas : '' }}
                                </span>
                                @if($item->total_penerima >= $totalStudents && $totalStudents > 0)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200/60 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">groups</span>
                                        Satu Kelas
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200/60 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">person</span>
                                        Perorangan ({{ $item->total_penerima }} Siswa)
                                    </span>
                                @endif
                                <span class="text-xs text-gray-400 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">schedule</span>
                                    {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y, H:i') }} WIB
                                </span>
                            </div>
                            <h2 class="text-base sm:text-lg font-bold text-navy hover:text-teal-accent-dark transition-colors" style="font-family:'Manrope',sans-serif;">
                                <a href="{{ route('bendahara.announcements.show', $item->id) }}">
                                    {{ $item->judul }}
                                </a>
                            </h2>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-1.5 self-end sm:self-start flex-shrink-0">
                            <a href="{{ route('bendahara.announcements.show', $item->id) }}" 
                               class="py-1.5 px-3 text-xs font-semibold text-navy bg-navy/5 hover:bg-navy/10 rounded-lg inline-flex items-center gap-1 transition-colors"
                               title="Lihat Rincian & Status Pembaca">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                                <span class="hidden sm:inline">Detail</span>
                            </a>
                            <a href="{{ route('bendahara.announcements.edit', $item->id) }}" 
                               class="py-1.5 px-3 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg inline-flex items-center gap-1 transition-colors"
                               title="Edit Pengumuman">
                                <span class="material-symbols-outlined text-sm">edit</span>
                                <span class="hidden sm:inline">Edit</span>
                            </a>
                            <form method="POST" action="{{ route('bendahara.announcements.destroy', $item->id) }}" class="m-0"
                                  onsubmit="return swalConfirm(event, 'Hapus Pengumuman?', 'Pengumuman ini beserta riwayat keterbacaan siswa akan dihapus permanen.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="py-1.5 px-3 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg inline-flex items-center gap-1 transition-colors"
                                        title="Hapus Pengumuman">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                    <span class="hidden sm:inline">Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Announcement Body Snippet -->
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed line-clamp-2 sm:line-clamp-3 mb-4 whitespace-pre-line">
                        {{ \Illuminate\Support\Str::limit(strip_tags($item->isi), 220) }}
                    </p>

                    <!-- Read Status Progress Footer -->
                    <div class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5">
                            <div class="w-24 sm:w-32 bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-teal-accent h-full rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="font-bold text-navy">{{ $pct }}%</span>
                            <span class="text-gray-400">({{ $item->dibaca_penerima }}/{{ $item->total_penerima }} Siswa Membaca)</span>
                        </div>

                        <div>
                            @if($unread === 0 && $item->total_penerima > 0)
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">done_all</span>
                                    Semua Siswa Telah Membaca
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/60 inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">hourglass_top</span>
                                    {{ $unread }} Siswa Belum Membaca
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        <div class="mt-4">
            {{ $announcements->links() }}
        </div>
    </div>
@endif

</x-app-layout>
