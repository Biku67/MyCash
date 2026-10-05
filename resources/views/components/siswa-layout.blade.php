<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MyCash') }} — Siswa</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/logo-mycash.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Work+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <!-- jQuery & DataTables -->
    <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/datatables/jquery.dataTables.min.css') }}">
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}">
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { 
            background: #FDF2DE; 
            background-image: linear-gradient(to right, rgba(27, 79, 114, 0.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(27, 79, 114, 0.07) 1px, transparent 1px); 
            background-size: 10px 10px; 
            font-family: 'Work Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            color: #0F172A; 
            -webkit-font-smoothing: antialiased; 
        }
        .font-heading { font-family: 'Manrope', sans-serif; letter-spacing: -0.02em; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .fill-icon { font-variation-settings: 'FILL' 1; }
        .sidebar { background: #1B4F72; }
        .sidebar-link { color: rgba(255,255,255,0.65); transition: all 0.2s; border-radius: 8px; font-size: 14px; }
        .sidebar-link:hover { color: #fff; background: rgba(255,255,255,0.1); }
        .sidebar-link.active { color: #fff; background: rgba(93,202,165,0.15); border-left: 3px solid #5DCAA5; font-weight: 600; }
        .topbar { background: rgba(255,255,255,0.92); backdrop-filter: blur(12px); border-bottom: 1px solid #E2E8F0; }
        .card { 
            background-color: #FFFFFF;
            border-radius: 14px; 
            border: 1px solid #E2E8F0;
            box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.03);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover { 
            border-color: #CBD5E1;
            box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.05); 
        }
        .table-clean th { color: #64748B; font-weight: 700; text-transform: uppercase; font-size: 0.6875rem; letter-spacing: 0.05em; padding: 0.625rem 0.875rem; border-bottom: 1px solid #E2E8F0; text-align: left; background-color: #F8FAFC; white-space: nowrap; }
        .table-clean td { padding: 0.625rem 0.875rem; border-bottom: 1px solid #F1F5F9; color: #334155; font-size: 0.8125rem; text-align: left; vertical-align: middle; }
        .table-clean tr:hover td { background-color: #F8FAFC; }
        
        /* ─── DataTables Modern Premium & Mobile-First Styling ─── */
        .dataTables_wrapper { font-family: 'Work Sans', sans-serif; padding: 1rem; }
        @media (min-width: 640px) { .dataTables_wrapper { padding: 1.25rem; } }
        .dataTables_wrapper .dataTables_length { margin-bottom: 0.75rem; color: #64748B; font-size: 0.8125rem; display: inline-flex; align-items: center; }
        .dataTables_wrapper .dataTables_length select { border: 1px solid #E2E8F0; border-radius: 0.5rem; padding: 0.35rem 1.75rem 0.35rem 0.65rem; font-size: 0.8125rem; color: #1E293B; background-color: #F8FAFC; outline: none; margin: 0 0.35rem; }
        .dataTables_wrapper .dataTables_filter { margin-bottom: 0.75rem; width: 100%; }
        @media (min-width: 640px) { .dataTables_wrapper .dataTables_filter { width: auto; } }
        .dataTables_wrapper .dataTables_filter label { display: flex; align-items: center; width: 100%; position: relative; font-size: 0; color: transparent; }
        .dataTables_wrapper .dataTables_filter label::before { content: 'search'; font-family: 'Material Symbols Outlined'; position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); font-size: 1.125rem; color: #94A3B8; pointer-events: none; }
        .dataTables_wrapper .dataTables_filter input { border: 1px solid #E2E8F0; border-radius: 0.75rem; padding: 0.45rem 0.85rem 0.45rem 2.25rem !important; font-size: 0.8125rem !important; color: #1E293B; width: 100% !important; outline: none; background-color: #F8FAFC; margin-left: 0 !important; }
        @media (min-width: 640px) { .dataTables_wrapper .dataTables_filter input { width: 240px !important; } }
        table.dataTable { border-collapse: collapse !important; width: 100% !important; margin: 0.5rem 0 !important; border: none !important; }
        table.dataTable thead th { background-color: #F8FAFC !important; border-bottom: 1px solid #E2E8F0 !important; color: #475569 !important; font-weight: 700 !important; font-size: 0.6875rem !important; text-transform: uppercase !important; padding: 0.625rem 0.875rem !important; white-space: nowrap; }
        table.dataTable tbody tr { background-color: #FFFFFF !important; }
        table.dataTable tbody tr:hover { background-color: #F8FAFC !important; }
        table.dataTable tbody td { padding: 0.625rem 0.875rem !important; border-bottom: 1px solid #F1F5F9 !important; color: #334155; font-size: 0.8125rem; vertical-align: middle; }
        .dataTables_wrapper .dataTables_info { color: #94A3B8 !important; font-size: 0.75rem !important; padding-top: 0.75rem; text-align: center; }
        @media (min-width: 640px) { .dataTables_wrapper .dataTables_info { text-align: left; font-size: 0.8125rem !important; } }
        .dataTables_wrapper .dataTables_paginate { padding-top: 0.75rem; display: flex; align-items: center; justify-content: center; gap: 0.25rem; flex-wrap: wrap; }
        @media (min-width: 640px) { .dataTables_wrapper .dataTables_paginate { justify-content: flex-end; } }
        .dataTables_wrapper .dataTables_paginate .paginate_button { border: 1px solid #E2E8F0 !important; border-radius: 0.5rem !important; padding: 0.3rem 0.6rem !important; font-size: 0.75rem !important; font-weight: 500 !important; color: #475569 !important; background: #FFFFFF !important; margin: 0 !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { color: #FFFFFF !important; background: #1B4F72 !important; border-color: #1B4F72 !important; }

        /* Responsive Card Table for Mobile (< 768px) */
        @media (max-width: 767px) {
            .table-responsive-cards thead {
                display: none !important;
            }
            .table-responsive-cards,
            .table-responsive-cards tbody,
            .table-responsive-cards tr,
            .table-responsive-cards td {
                display: block !important;
                width: 100% !important;
            }
            .table-responsive-cards tbody {
                display: flex !important;
                flex-direction: column !important;
                gap: 0.75rem !important;
            }
            .table-responsive-cards tr {
                background: #ffffff !important;
                border: 1px solid #E2E8F0 !important;
                border-radius: 0.875rem !important;
                padding: 0.875rem 1rem !important;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
                position: relative !important;
                transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
            }
            .table-responsive-cards tr:hover {
                border-color: #CBD5E1 !important;
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.05) !important;
            }
            .table-responsive-cards td {
                display: flex !important;
                flex-direction: column !important;
                align-items: flex-start !important;
                justify-content: flex-start !important;
                padding: 0.45rem 0 !important;
                border: none !important;
                border-bottom: 1px dashed #F1F5F9 !important;
                font-size: 0.8125rem !important;
                min-height: auto !important;
                white-space: normal !important;
                text-align: left !important;
                width: 100% !important;
            }
            .table-responsive-cards td > * {
                width: 100% !important;
                text-align: left !important;
            }
            .table-responsive-cards td:last-child {
                border-bottom: none !important;
                padding-top: 0.5rem !important;
                padding-bottom: 0 !important;
            }
            .table-responsive-cards td:last-child > * {
                display: flex !important;
                justify-content: flex-start !important;
                gap: 0.5rem !important;
            }
            .table-responsive-cards td::before {
                content: attr(data-label);
                font-size: 0.625rem !important;
                font-weight: 700 !important;
                color: #94A3B8 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.05em !important;
                text-align: left !important;
                margin-bottom: 0.2rem !important;
                margin-right: 0 !important;
                display: block !important;
                width: 100% !important;
            }
            .table-responsive-cards td[data-label="No"] {
                display: none !important;
            }
            .table-responsive-cards td[colspan] {
                display: block !important;
                text-align: center !important;
                justify-content: center !important;
                border-bottom: none !important;
                padding: 1.5rem 0 !important;
            }
            .table-responsive-cards td[colspan]::before {
                display: none !important;
            }
        }
    </style>
</head>
<body x-data="{ sidebarOpen: false, fluidMenuOpen: false }" class="min-h-screen">
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false" class="fixed inset-0 bg-black/30 z-40 lg:hidden" style="display:none;"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="sidebar fixed top-0 left-0 z-50 w-[220px] h-full flex flex-col justify-between overflow-y-auto transition-transform duration-300 lg:translate-x-0">
        <div class="p-5 flex items-center justify-between">
            <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('assets/logo-mycash.png') }}" alt="Logo MyCash" class="w-10 h-10 object-contain">
                <span class="text-lg font-bold text-white tracking-tight" style="font-family:'Manrope',sans-serif;">MyCash</span>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-white/60 hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="px-5 mb-5">
            <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-teal-accent bg-teal-accent/15 rounded-full">Siswa</span>
            <p class="text-xs text-white/50 mt-2 px-1 truncate">{{ Auth::user()->school_name ?? 'Sekolah' }}</p>
        </div>
        <nav class="flex-1 px-3 space-y-1">
            @php 
                $unreadCount = (Auth::user() && Auth::user()->siswa) 
                    ? Auth::user()->siswa->pengumumanPenerima()->where('is_read', false)->count() 
                    : 0; 
                $initialLatestNotifId = (Auth::user() && Auth::user()->siswa)
                    ? (Auth::user()->siswa->pengumumanPenerima()->max('id') ?? 0)
                    : 0;
            @endphp
            <a href="{{ route('siswa.dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-xl {{ request()->routeIs('siswa.dashboard') ? 'fill-icon' : '' }}">dashboard</span>
                Dashboard
            </a>
            <a href="{{ route('siswa.history.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 {{ request()->routeIs('siswa.history.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-xl {{ request()->routeIs('siswa.history.*') ? 'fill-icon' : '' }}">history</span>
                Riwayat Pembayaran
            </a>
            <a href="{{ route('siswa.notifications.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 {{ request()->routeIs('siswa.notifications.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-xl {{ request()->routeIs('siswa.notifications.*') ? 'fill-icon' : '' }}">notifications</span>
                Notifikasi
                <span id="sidebar-notif-badge" class="ml-auto bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full {{ $unreadCount > 0 ? '' : 'hidden' }}">{{ $unreadCount }}</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-xl {{ request()->routeIs('profile.*') ? 'fill-icon' : '' }}">person</span>
                Profil
            </a>
        </nav>
        <div class="p-4 border-t border-white/10">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-teal-accent/20 flex items-center justify-center">
                    <span class="text-sm font-bold text-teal-accent">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-white/40 truncate">NIS: {{ Auth::user()->siswa?->nis ?? '-' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" onsubmit="return swalConfirmLogout(event)">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-white/50 hover:text-red-300 hover:bg-red-400/10 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-lg">logout</span>Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="lg:ml-[220px]">
        <header class="topbar sticky top-0 z-30 px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
            <!-- Mobile Brand Logo (Kiri Atas) -->
            <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-2.5 lg:hidden">
                <img src="{{ asset('assets/logo-mycash.png') }}" alt="Logo MyCash" class="w-8 h-8 object-contain">
                <span class="text-base sm:text-lg font-bold text-navy font-heading tracking-tight">MyCash</span>
            </a>

            <!-- Desktop Page Title -->
            <div class="hidden lg:block">
                <h2 class="text-lg font-bold text-navy" style="font-family:'Manrope',sans-serif;">@yield('page-title', 'Dashboard')</h2>
            </div>

            <!-- Topbar Right Items (User Name on Mobile; Date + User Name + Logout on Desktop) -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                <span class="text-xs text-gray-400 hidden lg:block">{{ now()->format('d M Y') }}</span>
                <span class="text-xs font-semibold px-2.5 py-1 bg-navy/10 text-navy rounded-full truncate max-w-[150px] sm:max-w-none">{{ Auth::user()->name }}</span>
                <a href="{{ route('siswa.notifications.index') }}" class="relative p-1.5 text-gray-400 hover:text-navy hover:bg-gray-100 rounded-lg transition-colors flex items-center justify-center" title="Pusat Notifikasi">
                    <span class="material-symbols-outlined text-xl">notifications</span>
                    <span id="topbar-notif-badge" class="absolute top-1 right-1 w-2 h-2 rounded-full bg-rose-500 animate-pulse {{ $unreadCount > 0 ? '' : 'hidden' }}"></span>
                </a>
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('start-tour'))" class="p-1.5 text-gray-400 hover:text-navy hover:bg-gray-100 rounded-lg transition-colors flex items-center justify-center" title="Panduan Alur MyCash">
                    <span class="material-symbols-outlined text-xl">help</span>
                </button>
                <a href="{{ route('logout.get') }}" onclick="return swalConfirmLogout(event)" class="hidden lg:inline-flex p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Logout">
                    <span class="material-symbols-outlined text-xl">logout</span>
                </a>
            </div>
        </header>
        <main class="p-4 sm:p-6 lg:p-8 pb-24 lg:pb-8">
            {{ $slot }}
        </main>
    </div>

    <!-- ─── FLUID BACKDROP OVERLAY ─── -->
    <div x-show="fluidMenuOpen"
         x-transition:enter="transition-all duration-300 ease-out"
         x-transition:enter-start="opacity-0 backdrop-blur-none"
         x-transition:enter-end="opacity-100 backdrop-blur-sm"
         x-transition:leave="transition-all duration-200 ease-in"
         x-transition:leave-start="opacity-100 backdrop-blur-sm"
         x-transition:leave-end="opacity-0 backdrop-blur-none"
         @click="fluidMenuOpen = false"
         class="fixed inset-0 z-40 bg-black/25 lg:hidden"
         style="display:none;"></div>

    <!-- ─── FLOATING CIRCULAR ACTION BUTTONS ─── -->
    <div x-show="fluidMenuOpen"
         x-transition:enter="transition-all duration-350 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
         x-transition:enter-start="opacity-0 scale-50 translate-y-10"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition-all duration-200 ease-[cubic-bezier(0.4,0,1,1)]"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-50 translate-y-10"
         class="fixed bottom-[72px] right-4 z-50 flex flex-col items-center gap-3 lg:hidden"
         style="display:none;">
        
        <!-- 1. Profile -->
        <a href="{{ route('profile.edit') }}" @click="fluidMenuOpen = false"
           class="w-12 h-12 rounded-full bg-white text-gray-400 hover:text-navy shadow-[0_8px_20px_rgba(0,0,0,0.12)] border border-gray-100/80 flex items-center justify-center active:scale-85 hover:scale-105 transition-all relative"
           title="Profile">
            <span class="material-symbols-outlined text-2xl">person</span>
            @if(isset($unreadCount) && $unreadCount > 0)
                <span class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-red-500 ring-2 ring-white"></span>
            @endif
        </a>

        <!-- 2. Logout -->
        <form method="POST" action="{{ route('logout') }}" onsubmit="return swalConfirmLogout(event)" class="m-0 p-0">
            @csrf
            <button type="submit"
                    class="w-12 h-12 rounded-full bg-white border border-gray-100/80 flex items-center justify-center active:scale-85 hover:scale-105 transition-all focus:outline-none"
                    title="Keluar (Logout)">
                <span class="material-symbols-outlined text-2xl text-gray-400 hover:text-rose-500">logout</span>
            </button>
        </form>
    </div>

    <!-- ─── MOBILE BOTTOM NAVIGATION BAR ─── -->
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-xl border-t border-gray-200/90 py-1.5 px-3 flex items-center justify-around lg:hidden shadow-[0_-4px_20px_rgba(0,0,0,0.06)]">
        
        <!-- 1. Beranda Tab -->
        <a href="{{ route('siswa.dashboard') }}"
           class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition-all {{ request()->routeIs('siswa.dashboard') ? 'text-navy font-bold' : 'text-gray-400 hover:text-gray-600' }}">
            <span class="material-symbols-outlined text-2xl {{ request()->routeIs('siswa.dashboard') ? 'fill-icon text-navy' : '' }}">dashboard</span>
            <span class="text-[10px] tracking-tight">Beranda</span>
        </a>
        
        <!-- 2. Riwayat Pembayaran Tab -->
        <a href="{{ route('siswa.history.index') }}"
           class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition-all {{ request()->routeIs('siswa.history.*') ? 'text-navy font-bold' : 'text-gray-400 hover:text-gray-600' }}">
            <span class="material-symbols-outlined text-2xl {{ request()->routeIs('siswa.history.*') ? 'fill-icon text-navy' : '' }}">history</span>
            <span class="text-[10px] tracking-tight">Riwayat</span>
        </a>

        <!-- 3. Notifikasi Tab -->
        <a href="{{ route('siswa.notifications.index') }}"
           class="relative flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition-all {{ request()->routeIs('siswa.notifications.*') ? 'text-navy font-bold' : 'text-gray-400 hover:text-gray-600' }}">
            <div class="relative">
                <span class="material-symbols-outlined text-2xl {{ request()->routeIs('siswa.notifications.*') ? 'fill-icon text-navy' : '' }}">notifications</span>
                <span id="mobile-notif-badge" class="absolute -top-1 -right-2 min-w-[16px] h-4 px-1 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center {{ $unreadCount > 0 ? '' : 'hidden' }}">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            </div>
            <span class="text-[10px] tracking-tight">Notifikasi</span>
        </a>

        <!-- 4. FLUID MENU TRIGGER (Icon Transition: Menu <-> X) -->
        <button @click="fluidMenuOpen = !fluidMenuOpen"
                class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition-all focus:outline-none group active:scale-90"
                :class="fluidMenuOpen ? 'text-navy font-bold' : 'text-gray-400 hover:text-gray-600'">
            
            <div class="relative w-6 h-6 flex items-center justify-center">
                <!-- Menu Icon (Visible when closed) -->
                <div class="absolute inset-0 flex items-center justify-center transition-all duration-300 ease-in-out origin-center"
                     :class="fluidMenuOpen ? 'opacity-0 scale-0 rotate-180' : 'opacity-100 scale-100 rotate-0'">
                    <span class="material-symbols-outlined text-2xl">menu</span>
                </div>
                <!-- X Close Icon (Visible when open) -->
                <div class="absolute inset-0 flex items-center justify-center transition-all duration-300 ease-in-out origin-center text-rose-500"
                     :class="fluidMenuOpen ? 'opacity-100 scale-100 rotate-0' : 'opacity-0 scale-0 -rotate-180'">
                    <span class="material-symbols-outlined text-2xl">close</span>
                </div>
            </div>
            
            <span class="text-[10px] tracking-tight" :class="fluidMenuOpen ? 'text-rose-500 font-semibold' : ''" x-text="fluidMenuOpen ? 'Tutup' : 'Lainnya'">Lainnya</span>
        </button>

    </nav>

    {{-- SweetAlert2 Flash Messages --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            showCloseButton: true,
            backdrop: false,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        @if(session('success'))
            Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
        @endif
        @if(session('error'))
            Toast.fire({ icon: 'error', title: '{{ session('error') }}' });
        @endif
    });
    function swalConfirm(event, message, detail) {
        event.preventDefault();
        Swal.fire({
            title: message || 'Apakah Anda yakin?',
            text: detail || 'Tindakan ini tidak dapat dibatalkan.',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#1B4F72', cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, lanjutkan', cancelButtonText: 'Batal', reverseButtons: true
        }).then((result) => { if (result.isConfirmed) event.target.submit(); });
        return false;
    }
    function swalConfirmLogout(event) {
        event.preventDefault();
        const element = event.currentTarget || event.target;
        const form = element ? element.closest('form') : null;
        const anchor = element ? element.closest('a') : null;

        Swal.fire({
            title: 'Konfirmasi Keluar',
            text: 'Apakah Anda yakin ingin keluar dari akun MyCash?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                if (form) {
                    form.submit();
                } else if (anchor && anchor.href) {
                    window.location.href = anchor.href;
                } else {
                    window.location.href = "{{ route('logout.get') }}";
                }
            }
        });
        return false;
    }

    // Global Logout Interceptor
    let isLoggingOut = false;
    document.addEventListener('click', function(e) {
        if (isLoggingOut) return;
        const trigger = e.target.closest('a[href*="logout"], form[action*="logout"] button, button[data-logout], .btn-logout');
        if (!trigger) return;

        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        const form = trigger.closest('form');
        const anchor = trigger.closest('a');

        Swal.fire({
            title: 'Konfirmasi Keluar',
            text: 'Apakah Anda yakin ingin keluar dari akun MyCash?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                isLoggingOut = true;
                if (form) {
                    HTMLFormElement.prototype.submit.call(form);
                } else if (anchor && anchor.href && !anchor.href.startsWith('javascript:')) {
                    window.location.href = anchor.href;
                } else {
                    window.location.href = "{{ route('logout.get') }}";
                }
            }
        });
    }, true);

    document.addEventListener('submit', function(e) {
        if (isLoggingOut) return;
        const form = e.target;
        if (form && form.action && form.action.includes('logout')) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            Swal.fire({
                title: 'Konfirmasi Keluar',
                text: 'Apakah Anda yakin ingin keluar dari akun MyCash?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    isLoggingOut = true;
                    HTMLFormElement.prototype.submit.call(form);
                }
            });
        }
    }, true);
    </script>
    <style>.swal-toast-custom { font-family: 'Work Sans', sans-serif !important; font-size: 14px !important; }</style>
    {{-- Realtime Notification Floating Toast --}}
    <div x-data="realtimeNotificationWatcher({{ $initialLatestNotifId }}, {{ $unreadCount }})"
         x-init="initWatcher()"
         class="fixed top-4 right-4 sm:top-6 sm:right-6 z-50 max-w-sm w-full pointer-events-none px-3 sm:px-0">
        <template x-for="notif in activeToasts" :key="notif.id">
            <div x-show="notif.visible"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-250 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
                 class="pointer-events-auto mb-3 bg-white/95 backdrop-blur-md border border-emerald-300/80 shadow-2xl rounded-2xl p-4 transition-all duration-200 hover:shadow-emerald-500/10">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center flex-shrink-0 mt-0.5 shadow-sm">
                        <span class="material-symbols-outlined text-2xl" x-text="notif.is_payment ? 'payments' : 'notifications_active'"></span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800"
                                  x-text="notif.is_payment ? 'Pembayaran Kas' : 'Pengumuman Baru'">
                            </span>
                            <span class="text-[11px] text-gray-400 font-medium" x-text="notif.time"></span>
                        </div>
                        <h4 class="text-xs sm:text-sm font-bold text-navy leading-snug" style="font-family:'Manrope',sans-serif;" x-text="notif.judul"></h4>
                        <p class="text-[11px] sm:text-xs text-gray-600 mt-1 line-clamp-2 leading-relaxed" x-text="notif.isi"></p>
                        <div class="mt-2.5 flex items-center gap-2">
                            <a :href="notif.url"
                               class="py-1 px-3 bg-navy hover:bg-navy-light text-white text-[11px] font-semibold rounded-lg inline-flex items-center gap-1 transition-colors shadow-sm">
                                <span>Buka Notifikasi</span>
                                <span class="material-symbols-outlined text-xs">arrow_forward</span>
                            </a>
                            <button type="button" @click="dismissToast(notif.id)"
                                    class="py-1 px-2 text-gray-400 hover:text-gray-600 text-[11px] rounded-lg transition-colors">
                                Tutup
                            </button>
                        </div>
                    </div>
                    <button type="button" @click="dismissToast(notif.id)" class="text-gray-400 hover:text-gray-600 -mr-1 -mt-1 p-1 rounded-lg">
                        <span class="material-symbols-outlined text-base">close</span>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <script>
    function realtimeNotificationWatcher(initialLastId, initialUnread) {
        return {
            lastId: initialLastId || 0,
            unreadCount: initialUnread || 0,
            activeToasts: [],
            pollInterval: null,

            initWatcher() {
                this.startPolling();

                // Poll immediately when tab becomes active again
                document.addEventListener('visibilitychange', () => {
                    if (document.visibilityState === 'visible') {
                        this.checkNewNotifications();
                    }
                });
            },

            startPolling() {
                if (this.pollInterval) clearInterval(this.pollInterval);
                this.pollInterval = setInterval(() => {
                    if (document.visibilityState === 'visible') {
                        this.checkNewNotifications();
                    }
                }, 6000);
            },

            async checkNewNotifications() {
                try {
                    const response = await fetch(`{{ route('siswa.notifications.checkUnread') }}?last_id=${this.lastId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) return;

                    const data = await response.json();
                    if (data.success) {
                        if (data.last_id) {
                            this.lastId = Math.max(this.lastId, data.last_id);
                        }
                        this.updateBadges(data.unread_count);

                        if (data.has_new && data.notification) {
                            this.showToast(data.notification);
                            window.dispatchEvent(new CustomEvent('new-notification-received', { detail: data.notification }));
                        }
                    }
                } catch (err) {
                    // Silently ignore network fluctuations
                }
            },

            updateBadges(count) {
                this.unreadCount = count;

                // Update topbar dot
                const topbarDot = document.getElementById('topbar-notif-badge');
                if (topbarDot) {
                    if (count > 0) topbarDot.classList.remove('hidden');
                    else topbarDot.classList.add('hidden');
                }

                // Update sidebar badge
                const sidebarBadge = document.getElementById('sidebar-notif-badge');
                if (sidebarBadge) {
                    if (count > 0) {
                        sidebarBadge.textContent = count;
                        sidebarBadge.classList.remove('hidden');
                    } else {
                        sidebarBadge.classList.add('hidden');
                    }
                }

                // Update mobile bottom nav badge
                const mobileBadge = document.getElementById('mobile-notif-badge');
                if (mobileBadge) {
                    if (count > 0) {
                        mobileBadge.textContent = count > 9 ? '9+' : count;
                        mobileBadge.classList.remove('hidden');
                    } else {
                        mobileBadge.classList.add('hidden');
                    }
                }
            },

            showToast(notif) {
                notif.visible = true;
                this.activeToasts.push(notif);
                this.playNotificationSound();

                // Auto dismiss after 8 seconds
                setTimeout(() => {
                    this.dismissToast(notif.id);
                }, 8000);
            },

            dismissToast(id) {
                const index = this.activeToasts.findIndex(t => t.id === id);
                if (index !== -1) {
                    this.activeToasts[index].visible = false;
                    setTimeout(() => {
                        this.activeToasts = this.activeToasts.filter(t => t.id !== id);
                    }, 300);
                }
            },

            playNotificationSound() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const now = ctx.currentTime;
                    
                    // Note 1 (E5)
                    const osc1 = ctx.createOscillator();
                    const gain1 = ctx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(659.25, now);
                    gain1.gain.setValueAtTime(0.12, now);
                    gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                    osc1.connect(gain1);
                    gain1.connect(ctx.destination);
                    osc1.start(now);
                    osc1.stop(now + 0.35);

                    // Note 2 (B5)
                    const osc2 = ctx.createOscillator();
                    const gain2 = ctx.createGain();
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(987.77, now + 0.12);
                    gain2.gain.setValueAtTime(0.15, now + 0.12);
                    gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
                    osc2.connect(gain2);
                    gain2.connect(ctx.destination);
                    osc2.start(now + 0.12);
                    osc2.stop(now + 0.6);
                } catch (e) {
                    // Audio not allowed by browser autoplay policy until user interacted
                }
            }
        };
    }
    </script>
    <x-onboarding-tour />
        @stack('scripts')
</body>
</html>
