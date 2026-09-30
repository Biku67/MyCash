<x-app-layout>
@section('page-title', 'Notifikasi & Pengumuman')

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Page Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 font-heading">Notifikasi & Pengumuman</h1>
                <span id="page-unread-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500 text-white {{ $unreadCount > 0 ? '' : 'hidden' }}">
                    {{ $unreadCount }} Baru
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                Pemberitahuan setoran kas dan pengumuman resmi dari bendahara kelas Anda.
            </p>
        </div>

        <form id="mark-all-read-form" method="POST" action="{{ route('siswa.notifications.markAllAsRead') }}" class="m-0 {{ $unreadCount > 0 ? '' : 'hidden' }}">
            @csrf
            <button type="submit" 
                    class="py-2.5 px-4 text-xs sm:text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl inline-flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-base">done_all</span>
                <span>Tandai Semua Dibaca</span>
            </button>
        </form>
    </div>

    <!-- Notifications List Container -->
    <div id="tour-siswa-notifications-list">
        @if($notifications->isEmpty())
            <div id="empty-notification-placeholder" class="card p-8 sm:p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center mx-auto mb-3.5">
                    <span class="material-symbols-outlined text-2xl">notifications_off</span>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900 font-heading mb-1">Belum Ada Notifikasi</h3>
                <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                    Saat ini belum ada pengumuman atau riwayat setoran baru dari bendahara kelas Anda.
                </p>
            </div>
            <div id="notifications-container" class="space-y-3.5"></div>
        @else
            <div id="notifications-container" class="space-y-3.5">
                @foreach($notifications as $item)
                    @include('siswa.notifications.partials.item', ['item' => $item])
                @endforeach
            </div>

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.addEventListener('new-notification-received', function(e) {
        const emptyPlaceholder = document.getElementById('empty-notification-placeholder');
        if (emptyPlaceholder) {
            emptyPlaceholder.remove();
        }

        const container = document.getElementById('notifications-container');
        if (container && e.detail && e.detail.card_html) {
            // Prevent duplicate insertion if already in DOM
            if (!document.getElementById(`notif-item-${e.detail.id}`)) {
                container.insertAdjacentHTML('afterbegin', e.detail.card_html);
                
                const newEl = document.getElementById(`notif-item-${e.detail.id}`);
                if (newEl) {
                    newEl.classList.add('ring-2', 'ring-emerald-400', 'bg-emerald-50/40');
                    setTimeout(() => {
                        newEl.classList.remove('ring-2', 'ring-emerald-400');
                    }, 3500);
                }
            }
        }

        // Show mark-all-as-read button & update header badge
        const markForm = document.getElementById('mark-all-read-form');
        if (markForm) markForm.classList.remove('hidden');

        const pageBadge = document.getElementById('page-unread-badge');
        if (pageBadge) {
            pageBadge.classList.remove('hidden');
            const currentNum = parseInt(pageBadge.textContent) || 0;
            pageBadge.textContent = `${currentNum + 1} Baru`;
        }
    });
});
</script>
@endpush

</x-app-layout>
