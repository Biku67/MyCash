<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('assets/logo-mycash.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="text-slate-800 antialiased" style="font-family: 'Work Sans', sans-serif; background: #FDF2DE; background-image: linear-gradient(to right, rgba(27, 79, 114, 0.07) 1px, transparent 1px), linear-gradient(to bottom, rgba(27, 79, 114, 0.07) 1px, transparent 1px); background-size: 10px 10px;">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-8 sm:pt-0 px-4">
            <div>
                <a href="/">
                    <img src="{{ asset('assets/logo-mycash.png') }}" alt="Logo MyCash" class="w-16 h-16 object-contain">
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
