<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MyCash') }} — Wali Kelas</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/logo-mycash.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Work+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
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
        
        .btn-navy { background: #1B4F72; color: #fff; font-weight: 600; border-radius: 8px; transition: all 0.2s; }
        .btn-navy:hover { background: #143D59; }

        /* ─── Flatpickr Custom Styling matching MyCash Design ─── */
        .flatpickr-calendar {
            background: #ffffff !important;
            border-radius: 16px !important;
            border: 1px solid rgba(27, 79, 114, 0.12) !important;
            box-shadow: 0 16px 36px -6px rgba(27, 79, 114, 0.2), 0 4px 12px rgba(0, 0, 0, 0.05) !important;
            font-family: 'Work Sans', sans-serif !important;
            padding: 8px !important;
        }
        .flatpickr-calendar .flatpickr-months {
            background: #1B4F72 !important;
            border-radius: 12px 12px 0 0 !important;
            padding: 8px 0 !important;
        }
        .flatpickr-calendar .flatpickr-current-month {
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 105% !important;
        }
        .flatpickr-calendar .flatpickr-current-month .cur-month {
            font-weight: 700 !important;
            color: #ffffff !important;
        }
        .flatpickr-calendar .flatpickr-current-month input.cur-year {
            font-weight: 700 !important;
            color: #ffffff !important;
        }
        .flatpickr-calendar .flatpickr-prev-month svg, 
        .flatpickr-calendar .flatpickr-next-month svg {
            fill: #ffffff !important;
        }
        .flatpickr-calendar .flatpickr-prev-month:hover svg, 
        .flatpickr-calendar .flatpickr-next-month:hover svg {
            fill: #5DCAA5 !important;
        }
        .flatpickr-calendar span.flatpickr-weekday {
            color: #1B4F72 !important;
            font-weight: 700 !important;
            font-size: 85% !important;
        }
        .flatpickr-calendar .flatpickr-day {
            border-radius: 10px !important;
            font-weight: 500 !important;
            color: #334155 !important;
            transition: all 0.15s ease !important;
        }
        .flatpickr-calendar .flatpickr-day:hover {
            background: #F1F5F9 !important;
            color: #1B4F72 !important;
        }
        .flatpickr-calendar .flatpickr-day.today {
            border-color: #5DCAA5 !important;
            color: #0d9488 !important;
            font-weight: 700 !important;
        }
        .flatpickr-calendar .flatpickr-day.selected {
            background: #1B4F72 !important;
            color: #ffffff !important;
            border-color: #1B4F72 !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 10px rgba(27, 79, 114, 0.25) !important;
        }
        .flatpickr-calendar .flatpickr-day.flatpickr-disabled, 
        .flatpickr-calendar .flatpickr-day.flatpickr-disabled:hover {
            color: #cbd5e1 !important;
            cursor: not-allowed !important;
            opacity: 0.35 !important;
        }

        .table-clean th { color: #64748B; font-weight: 700; text-transform: uppercase; font-size: 0.6875rem; letter-spacing: 0.05em; padding: 0.625rem 0.875rem; border-bottom: 1px solid #E2E8F0; text-align: left; background-color: #F8FAFC; white-space: nowrap; }
        .table-clean td { padding: 0.625rem 0.875rem; border-bottom: 1px solid #F1F5F9; color: #334155; font-size: 0.8125rem; text-align: left; vertical-align: middle; }
        .table-clean tr:hover td { background-color: #F8FAFC; }

        /* DataTables Modern & Mobile Styling */
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

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="sidebar fixed top-0 left-0 z-50 w-[220px] h-full flex flex-col transition-transform duration-300 lg:translate-x-0">
        <div class="p-5 flex items-center justify-between">
            <a href="{{ route('wali-kelas.dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('assets/logo-mycash.png') }}" alt="Logo MyCash" class="w-10 h-10 object-contain">
                <span class="text-lg font-bold text-white tracking-tight" style="font-family:'Manrope',sans-serif;">MyCash</span>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-white/60 hover:text-white">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="px-5 mb-5">
            <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-teal-accent bg-teal-accent/15 rounded-full">Wali Kelas</span>
        </div>
        <nav class="flex-1 px-3 space-y-1">
            <a href="{{ route('wali-kelas.dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 {{ request()->routeIs('wali-kelas.dashboard') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-xl {{ request()->routeIs('wali-kelas.dashboard') ? 'fill-icon' : '' }}">dashboard</span>
                Dashboard
            </a>
            <a href="{{ route('wali-kelas.students.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 {{ request()->routeIs('wali-kelas.students.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-xl {{ request()->routeIs('wali-kelas.students.*') ? 'fill-icon' : '' }}">groups</span>
                Data Siswa
            </a>
            <a href="{{ route('wali-kelas.report.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 {{ request()->routeIs('wali-kelas.report.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-xl {{ request()->routeIs('wali-kelas.report.*') ? 'fill-icon' : '' }}">description</span>
                Laporan Kas
            </a>
            <a href="{{ route('profile.edit') }}" class="sidebar-link flex items-center gap-3 px-4 py-2.5 {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined text-xl {{ request()->routeIs('profile.*') ? 'fill-icon' : '' }}">person</span>
                Profil
            </a>
        </nav>

        <div class="p-4 border-t border-white/10">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-white/15 flex items-center justify-center">
                    <span class="text-sm font-bold text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-white/40 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <a href="{{ route('logout.get') }}" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-white/50 hover:text-red-300 hover:bg-red-400/10 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-lg">logout</span>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <div class="lg:ml-[220px]">
        <header class="topbar sticky top-0 z-30 px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
            <!-- Mobile Brand Logo (Kiri Atas) -->
            <a href="{{ route('wali-kelas.dashboard') }}" class="flex items-center gap-2.5 lg:hidden">
                <img src="{{ asset('assets/logo-mycash.png') }}" alt="Logo MyCash" class="w-8 h-8 object-contain">
                <span class="text-base sm:text-lg font-bold text-navy font-heading tracking-tight">MyCash</span>
            </a>

            <!-- Desktop Page Title -->
            <div class="hidden lg:block">
                <h2 class="text-lg font-bold text-navy" style="font-family:'Manrope',sans-serif;">@yield('page-title', 'Dashboard Wali Kelas')</h2>
            </div>

            <!-- Topbar Right Items (User Name on Mobile; Date + User Name + Logout on Desktop) -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                <span class="text-xs text-gray-400 hidden lg:block">{{ now()->format('d M Y') }}</span>
                <span class="text-xs font-semibold px-2.5 py-1 bg-navy/10 text-navy rounded-full truncate max-w-[150px] sm:max-w-none">{{ Auth::user()->name }}</span>
                <button type="button" onclick="window.dispatchEvent(new CustomEvent('start-tour'))" class="p-1.5 text-gray-400 hover:text-navy hover:bg-gray-100 rounded-lg transition-colors flex items-center justify-center" title="Panduan Alur MyCash">
                    <span class="material-symbols-outlined text-xl">help</span>
                </button>
                <a href="{{ route('logout.get') }}" class="hidden lg:inline-flex p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Logout">
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
        
        <!-- 1. Profil Akun -->
        <a href="{{ route('profile.edit') }}" @click="fluidMenuOpen = false"
           class="w-12 h-12 rounded-full bg-white text-gray-400 hover:text-navy shadow-[0_8px_20px_rgba(0,0,0,0.12)] border border-gray-100/80 flex items-center justify-center active:scale-85 hover:scale-105 transition-all"
           title="Profil Akun">
            <span class="material-symbols-outlined text-2xl">person</span>
        </a>

        <!-- 2. Logout -->
        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
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
        
        <!-- 1. Dashboard Tab -->
        <a href="{{ route('wali-kelas.dashboard') }}"
           class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition-all {{ request()->routeIs('wali-kelas.dashboard') ? 'text-navy font-bold' : 'text-gray-400 hover:text-gray-600' }}">
            <span class="material-symbols-outlined text-2xl {{ request()->routeIs('wali-kelas.dashboard') ? 'fill-icon text-navy' : '' }}">dashboard</span>
            <span class="text-[10px] tracking-tight">Dashboard</span>
        </a>

        <!-- 2. Data Siswa Tab -->
        <a href="{{ route('wali-kelas.students.index') }}"
           class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition-all {{ request()->routeIs('wali-kelas.students.*') ? 'text-navy font-bold' : 'text-gray-400 hover:text-gray-600' }}">
            <span class="material-symbols-outlined text-2xl {{ request()->routeIs('wali-kelas.students.*') ? 'fill-icon text-navy' : '' }}">groups</span>
            <span class="text-[10px] tracking-tight">Siswa</span>
        </a>

        <!-- 3. Laporan Kas Tab -->
        <a href="{{ route('wali-kelas.report.index') }}"
           class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition-all {{ request()->routeIs('wali-kelas.report.*') ? 'text-navy font-bold' : 'text-gray-400 hover:text-gray-600' }}">
            <span class="material-symbols-outlined text-2xl {{ request()->routeIs('wali-kelas.report.*') ? 'fill-icon text-navy' : '' }}">description</span>
            <span class="text-[10px] tracking-tight">Laporan</span>
        </a>

        <!-- 4. Profil Tab -->
        <a href="{{ route('profile.edit') }}"
           class="flex flex-col items-center gap-0.5 py-1 px-3 rounded-xl transition-all {{ request()->routeIs('profile.*') ? 'text-navy font-bold' : 'text-gray-400 hover:text-gray-600' }}">
            <span class="material-symbols-outlined text-2xl {{ request()->routeIs('profile.*') ? 'fill-icon text-navy' : '' }}">person</span>
            <span class="text-[10px] tracking-tight">Profil</span>
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
            timer: 4000,
            timerProgressBar: true,
            showCloseButton: true,
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
        @if(session('warning'))
            Toast.fire({ icon: 'warning', title: '{{ session('warning') }}' });
        @endif
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof flatpickr !== 'undefined') {
            flatpickr.localize(flatpickr.l10ns.id);

            // Filter Laporan: Format tanggal bulan teks (contoh: 21 September 2026)
            document.querySelectorAll('.datepicker-report').forEach(function(el) {
                flatpickr(el, {
                    locale: 'id',
                    altInput: true,
                    altFormat: 'j F Y',
                    dateFormat: 'Y-m-d',
                    maxDate: el.getAttribute('max') || 'today',
                    disableMobile: true,
                    altInputClass: el.className.replace('datepicker-report', '').trim() + ' cursor-pointer'
                });
            });
        }
    });
    </script>
    <x-onboarding-tour />
    @stack('scripts')
</body>
</html>
