@php
    $announcement = $item->pengumuman;
    $isPaymentNotif = $announcement && str_contains($announcement->judul, 'Pembayaran Kas');
@endphp
@if($announcement)
    <a href="{{ route('siswa.notifications.show', $item->id) }}" 
       id="notif-item-{{ $item->id }}"
       class="block card p-5 transition-all duration-200 group {{ !$item->is_read ? ($isPaymentNotif ? 'bg-emerald-50/25 border-emerald-300 hover:border-emerald-400' : 'bg-teal-50/20 border-teal-300 hover:border-teal-400') : 'hover:border-slate-300' }}">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-3.5 min-w-0">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 {{ !$item->is_read ? ($isPaymentNotif ? 'bg-emerald-600 text-white' : 'bg-teal-accent text-white') : ($isPaymentNotif ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500') }}">
                    <span class="material-symbols-outlined text-xl">{{ $isPaymentNotif ? 'payments' : (!$item->is_read ? 'mark_chat_unread' : 'chat_bubble') }}</span>
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        @if(!$item->is_read)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500 text-white uppercase tracking-wider">
                                Baru
                            </span>
                        @endif
                        @if($isPaymentNotif)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">
                                Pembayaran Kas
                            </span>
                        @endif
                        <span class="text-xs text-slate-400 flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">schedule</span>
                            {{ \Carbon\Carbon::parse($announcement->created_at)->translatedFormat('d M Y, H:i') }} WIB
                        </span>
                        <span class="text-xs text-slate-300">&bull;</span>
                        <span class="text-xs text-slate-500 font-medium">
                            Dari: {{ $announcement->bendahara->user->name ?? 'Bendahara Kelas' }}
                        </span>
                    </div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-navy transition-colors font-heading">
                        {{ $announcement->judul }}
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1 line-clamp-2 leading-relaxed">
                        {{ \Illuminate\Support\Str::limit(strip_tags($announcement->isi), 180) }}
                    </p>
                </div>
            </div>
            <div class="self-center flex-shrink-0 text-slate-300 group-hover:text-navy group-hover:translate-x-0.5 transition-all">
                <span class="material-symbols-outlined text-xl">arrow_forward_ios</span>
            </div>
        </div>
    </a>
@endif
