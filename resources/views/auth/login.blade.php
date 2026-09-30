<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MyCash') }} - Login</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/logo-mycash.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Work Sans', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .fill-icon { font-variation-settings: 'FILL' 1; }
    </style>
</head>
<body class="min-h-screen flex bg-white font-sans antialiased text-slate-900">

    <!-- Left: Branding Panel -->
    <div class="hidden lg:flex w-1/2 relative bg-[#1B4F72] flex-col justify-center items-center overflow-hidden border-r border-slate-800">
        <div class="relative z-10 flex flex-col items-center text-center px-12 max-w-lg">
            <!-- Logo -->
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-6 shadow-sm">
                <img src="{{ asset('assets/logo-mycash.png') }}" alt="Logo MyCash" class="w-14 h-14 object-contain">
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight font-heading">MyCash</h1>
            <p class="text-sm sm:text-base text-slate-400 mt-3 leading-relaxed">
                Kelola kas kelas dengan sistem pencatatan yang transparan, otomatis, dan akuntabel.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-xs font-semibold text-slate-300">
                    <span class="material-symbols-outlined text-teal-400 text-base fill-icon">verified</span>
                    <span>Transparan</span>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-xs font-semibold text-slate-300">
                    <span class="material-symbols-outlined text-teal-400 text-base fill-icon">sync</span>
                    <span>Otomatis</span>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-xs font-semibold text-slate-300">
                    <span class="material-symbols-outlined text-teal-400 text-base fill-icon">shield</span>
                    <span>Akuntabel</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Login Form -->
    <div class="w-full lg:w-1/2 flex flex-col bg-white min-h-screen">
        <div class="flex-grow flex flex-col justify-center px-8 sm:px-16 lg:px-20 py-12">
            <div class="w-full max-w-[420px] mx-auto">
                <!-- Mobile Logo -->
                <div class="flex lg:hidden items-center gap-3 mb-8">
                    <img src="{{ asset('assets/logo-mycash.png') }}" alt="Logo MyCash" class="w-12 h-12 object-contain">
                    <span class="text-xl font-extrabold text-slate-900 tracking-tight font-heading">MyCash</span>
                </div>

                <div class="mb-7">
                    <h2 class="text-2xl font-extrabold text-slate-900 font-heading tracking-tight">Masuk ke MyCash</h2>
                    <p class="text-slate-500 text-xs sm:text-sm mt-1">Masukkan kredensial akun untuk mengakses portal.</p>
                </div>

                @if (session('status'))
                    <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-5">
                    @csrf
                    <div>
                        <label for="login" class="block text-xs font-semibold text-slate-700 mb-1.5">Email atau NIS</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">mail</span>
                            <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus
                                class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:border-[#1B4F72] focus:ring-1 focus:ring-[#1B4F72] outline-none transition-all shadow-sm"
                                placeholder="nama@email.com atau NIS">
                        </div>
                        @error('login')<p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>

                    <div x-data="{ showPassword: false }">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-semibold text-slate-700">Password</label>
                            @if (Route::has('password.request'))
                                <a class="text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors" href="{{ route('password.request') }}">Lupa password?</a>
                            @endif
                        </div>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">lock</span>
                            <input id="password" :type="showPassword ? 'text' : 'password'" type="password" name="password" required autocomplete="current-password"
                                class="w-full pl-10 pr-11 py-2.5 sm:py-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 placeholder:text-slate-400 focus:border-[#1B4F72] focus:ring-1 focus:ring-[#1B4F72] outline-none transition-all shadow-sm"
                                placeholder="••••••••">
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 focus:outline-none flex items-center justify-center p-1 rounded-lg hover:bg-slate-100 transition-colors"
                                aria-label="Tampilkan atau sembunyikan password">
                                <span class="material-symbols-outlined text-lg" x-text="showPassword ? 'visibility_off' : 'visibility'">visibility</span>
                            </button>
                        </div>
                        @error('password')<p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center pt-0.5">
                        <input id="remember_me" type="checkbox" name="remember"
                            class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900/20">
                        <label for="remember_me" class="ml-2 text-xs sm:text-sm text-slate-600 cursor-pointer select-none">Ingat saya</label>
                    </div>

                    <button type="submit" class="w-full py-2.5 sm:py-3 bg-[#1B4F72] hover:bg-slate-800 text-white rounded-xl text-xs sm:text-sm font-bold shadow-sm hover:shadow flex items-center justify-center gap-2 transition-all active:scale-[0.99]">
                        <span>Masuk ke Akun</span>
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </button>
                </form>

                <div class="mt-6 p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl text-center">
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Siswa login dengan <span class="text-slate-800 font-semibold">NIS</span> •
                        Guru & Admin dengan <span class="text-slate-800 font-semibold">Email</span>
                    </p>
                </div>
            </div>
        </div>

        <footer class="py-5 border-t border-slate-100 bg-white">
            <div class="flex flex-col md:flex-row justify-between items-center px-8 max-w-7xl mx-auto gap-2">
                <p class="text-xs text-slate-400 font-medium">© {{ date('Y') }} MyCash. Semua hak cipta dilindungi.</p>
                <div class="flex space-x-6">
                    <a class="text-xs text-slate-400 hover:text-slate-700 transition-colors" href="#">Kebijakan Privasi</a>
                    <a class="text-xs text-slate-400 hover:text-slate-700 transition-colors" href="#">Ketentuan Layanan</a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
