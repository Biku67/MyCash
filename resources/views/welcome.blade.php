<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    
    <!-- Primary SEO Metadata -->
    <title>MyCash — Aplikasi Kas Kelas Digital & Transparan untuk Sekolah</title>
    <meta name="description" content="Kelola kas kelas dengan akurat, cepat, dan transparan. Catat setoran iuran siswa, pantau pengeluaran, cetak laporan PDF/Excel, dan cegah selisih pembukuan.">
    <meta name="keywords" content="aplikasi kas kelas, iuran siswa, pembukuan kas sekolah, bendahara kelas, software kas kelas, audit trail kas">
    <meta name="author" content="MyCash">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="MyCash">
    <meta property="og:title" content="MyCash — Aplikasi Kas Kelas Digital & Transparan untuk Sekolah">
    <meta property="og:description" content="Kelola kas kelas dengan akurat, cepat, dan transparan. Catat setoran iuran siswa, pantau pengeluaran terbuka, dan cetak laporan resmi langsung dari ponsel.">
    <meta property="og:image" content="{{ asset('assets/logo-mycash.png') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="MyCash — Aplikasi Kas Kelas Digital & Transparan">
    <meta name="twitter:description" content="Catat iuran siswa tanpa pusing, pantau pengeluaran secara terbuka, dan cetak laporan keuangan kapan pun dibutuhkan.">
    <meta name="twitter:image" content="{{ asset('assets/logo-mycash.png') }}">

    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/logo-mycash.png') }}">
    
    <!-- Preload First Frame of 300-Frame Image Sequence for Instant Paint -->
    <link rel="preload" as="image" href="{{ asset('assets/sequence/ezgif-frame-001.jpg') }}" fetchpriority="high">

    <!-- Satoshi, Plus Jakarta Sans & Material Symbols Fonts -->
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@900,800,700,500,400&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Work+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <!-- Structured Data (JSON-LD) for SEO Rich Snippets -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'SoftwareApplication',
                'name' => 'MyCash',
                'applicationCategory' => 'BusinessApplication',
                'operatingSystem' => 'Web, Android',
                'offers' => [
                    '@type' => 'Offer',
                    'price' => '0',
                    'priceCurrency' => 'IDR'
                ],
                'description' => 'Aplikasi pembukuan kas kelas dan iuran siswa digital dengan pencatatan otomatis, audit trail transparan, dan laporan resmi PDF/Excel.'
            ],
            [
                '@type' => 'Organization',
                'name' => 'MyCash',
                'url' => url('/'),
                'logo' => asset('assets/logo-mycash.png')
            ],
            [
                '@type' => 'FAQPage',
                'mainEntity' => [
                    [
                        '@type' => 'Question',
                        'name' => 'Apakah MyCash bisa digunakan di HP tanpa perlu instal aplikasi?',
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => 'Bisa. MyCash dapat diakses langsung melalui browser smartphone (Chrome, Safari, Edge) secara responsif, dan juga tersedia aplikasi Android untuk kemudahan pencatatan bendahara.'
                        ]
                    ],
                    [
                        '@type' => 'Question',
                        'name' => 'Bagaimana jika bendahara salah memasukkan nominal kas?',
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => 'MyCash memiliki fitur Audit Trail lengkap. Setiap transaksi yang diubah atau dibatalkan akan terekam secara otomatis beserta nama bendahara, waktu perubahan, dan alasan revisi sehingga tidak ada kecurigaan selisih.'
                        ]
                    ],
                    [
                        '@type' => 'Question',
                        'name' => 'Apakah siswa dan wali kelas bisa memantau kas?',
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => 'Ya. MyCash memisahkan hak akses: bendahara bertugas mencatat dan memvalidasi kas, wali kelas memantau laporan kelas, dan siswa dapat memeriksa status pembayaran serta saldo kas kapan pun secara transparan.'
                        ]
                    ],
                    [
                        '@type' => 'Question',
                        'name' => 'Format apa saja yang disediakan untuk ekspor laporan kas?',
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => 'Laporan kas bulanan maupun semesteran dapat diunduh sekali klik ke format Microsoft Excel (.xlsx) dan format PDF resmi yang siap dicetak dan diserahkan ke wali kelas atau pihak sekolah.'
                        ]
                    ],
                    [
                        '@type' => 'Question',
                        'name' => 'Apakah data kas kelas aman tersimpan di cloud?',
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => 'Seluruh data kas kelas tersimpan aman di database terenkripsi dengan backup berkala. Jika ponsel bendahara hilang atau rusak, seluruh rekaman kas tetap aman dan dapat diakses kembali.'
                        ]
                    ]
                ]
            ]
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>

    <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/landing.js'])

    <style>
        :root {
            --font-satoshi: 'Satoshi', 'Plus Jakarta Sans', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
            --color-navy: #1B4F72;
            --color-navy-light: #2471A3;
            --color-navy-dark: #154360;
            --color-navy-deep: #0B192C;
            --color-blue-accent: #2471A3;
            --color-blue-vibrant: #38BDF8;
            --color-light: #F8FAFC;
        }

        html, body {
            font-family: var(--font-satoshi);
            background-color: #060D17;
            color: #FFFFFF;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        .font-mono-data {
            font-family: var(--font-mono);
            font-feature-settings: "tnum" 1;
        }

        /* Subtle Architectural Grid Lines Pattern */
        .bg-grid-subtle {
            background-size: 44px 44px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.035) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
        }
        .bg-grid-subtle-light {
            background-size: 44px 44px;
            background-image: 
                linear-gradient(to right, rgba(0, 0, 0, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(0, 0, 0, 0.03) 1px, transparent 1px);
        }

        /* Bendahara Blue Button with Slide Arrow */
        .btn-cip-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: #1B4F72;
            color: #FFFFFF;
            border-radius: 9999px;
            padding: 0.5rem 1.35rem;
            font-weight: 600;
            font-size: 0.8125rem;
            letter-spacing: -0.01em;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(255, 255, 255, 0.16);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }
        .btn-cip-primary:hover {
            background: #2471A3;
            border-color: #38BDF8;
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 4px 18px rgba(36, 113, 163, 0.45);
        }
        .btn-cip-primary:active {
            transform: scale(0.97);
        }

        /* Material symbols style */
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
        }
        .fill-icon {
            font-variation-settings: 'FILL' 1;
        }
    </style>
</head>
<body x-data="{ 
        scrolled: false,
        activeTab: 0,
        mobileMenuOpen: false,
        whatsappModalOpen: false,
        activeFaq: null,
        tabs: [
            {
                title: 'Pencatatan Kas & Checklist Siswa',
                description: 'Catat iuran mingguan atau bulanan via checklist nama siswa. Saldo terhitung otomatis tanpa kalkulator manual dan tanpa risiko buku catatan rusak.',
                tag: 'Checklist Otomatis',
                stats: 'Cepat & Akurat'
            },
            {
                title: 'Riwayat & Jejak Audit Lengkap',
                description: 'Setiap transaksi yang diedit atau dibatalkan terekam otomatis: siapa yang mengubah, kapan dilakukan, dan apa alasannya. 100% transparan.',
                tag: 'Audit Trail',
                stats: 'Terekam Detail'
            },
            {
                title: 'Ekspor Laporan PDF & Excel Resmi',
                description: 'Sekali klik untuk unduh rekap kas bulanan atau semesteran. Format rapi berstandar resmi, lengkap dengan ringkasan dan tanda tangan.',
                tag: 'Sekali Klik',
                stats: 'Excel & PDF'
            },
            {
                title: 'Akses Khusus Tiap Peran Sekolah',
                description: 'Bendahara fokus mencatat kas, wali kelas memantau laporan kelas, dan siswa dapat memeriksa status pembayaran sendiri langsung dari HP.',
                tag: 'Hak Akses Ketat',
                stats: 'Aman & Teratur'
            }
        ]
    }" 
    @scroll.window="scrolled = (window.pageYOffset > 24)"
    class="relative selection:bg-navy selection:text-white">

    <!-- ─── TOP HEADER (Bendahara Blue Minimalist Style) ─── -->
    <header :class="scrolled ? 'bg-[#1B4F72] backdrop-blur-xl border-b border-white/[0.06] shadow-[0_4px_30px_rgba(0,0,0,0.35)] py-3.5 sm:py-4' : 'bg-transparent py-5 sm:py-6'"
            class="fixed top-0 left-0 w-full z-50 px-6 sm:px-12 flex justify-between items-center pointer-events-none transition-all duration-300">
        <!-- Brand Logo (lowercase with bendahara blue dot) -->
        <a href="/" class="text-xl sm:text-2xl font-extrabold tracking-tight text-white flex items-center gap-2.5 group pointer-events-auto select-none">
            <img src="{{ asset('assets/logo-mycash.png') }}" alt="Logo MyCash" class="w-7 h-7 sm:w-8 sm:h-8 object-contain transition-transform duration-300 group-hover:scale-105">
            <span class="tracking-tight">mycash<span class="text-sky-400 text-2xl sm:text-3xl font-black leading-none">.</span></span>
        </a>

        <!-- Center Navigation Links (Minimalist & Sleek) -->
        <nav class="hidden md:flex items-center gap-7 lg:gap-8 pointer-events-auto">
            <a href="#who" class="text-xs sm:text-[13px] font-medium text-slate-300 hover:text-sky-400 transition-colors duration-200 tracking-wide">Who</a>
            <a href="#what" class="text-xs sm:text-[13px] font-medium text-slate-300 hover:text-sky-400 transition-colors duration-200 tracking-wide">What</a>
            <a href="#why-us" class="text-xs sm:text-[13px] font-medium text-slate-300 hover:text-sky-400 transition-colors duration-200 tracking-wide">Why Us</a>
            <a href="#faq" class="text-xs sm:text-[13px] font-medium text-slate-300 hover:text-sky-400 transition-colors duration-200 tracking-wide">FAQ</a>
        </nav>

        <!-- Header Right Actions -->
        <div class="flex items-center gap-2.5 sm:gap-3 pointer-events-auto">
            <button @click="whatsappModalOpen = true"
                    class="w-9 h-9 rounded-full bg-white/5 hover:bg-white/10 text-slate-300 hover:text-sky-400 border border-white/10 flex items-center justify-center transition-all"
                    title="Hubungi WhatsApp">
                <span class="material-symbols-outlined text-base">chat</span>
            </button>

            @auth
                <a href="{{ url('/dashboard') }}" class="btn-cip-primary group">
                    <span>Dashboard</span>
                    <span class="material-symbols-outlined text-xs sm:text-sm group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform duration-200">arrow_outward</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-cip-primary group">
                    <span>Masuk</span>
                    <span class="material-symbols-outlined text-xs sm:text-sm group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform duration-200">arrow_outward</span>
                </a>
            @endauth
        </div>
    </header>

    <main>
        <!-- ─── SECTION 1 (MOBILE): MINIMALIST HERO (< 1024px) ─── -->
        <section id="hero-mobile" class="block lg:hidden relative min-h-[92vh] sm:min-h-screen bg-[#060D17] bg-grid-subtle flex flex-col justify-between pt-32 sm:pt-36 pb-20 px-6 sm:px-12 overflow-hidden select-none">
            
            <!-- Hero Background Image (Hero.png) -->
            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                <img src="{{ asset('assets/Hero.png') }}" 
                     alt="MyCash Hero Background" 
                     class="w-full h-full object-cover object-center opacity-70 select-none transition-opacity duration-500" />
                
                <!-- Dark Gradient Overlay for Maximum Text Readability -->
                <div class="absolute inset-0 bg-gradient-to-b from-[#060D17]/90 via-[#060D17]/50 to-[#060D17]"></div>
                
                <!-- Subtle Brand Glow Accents (Bendahara Blue) -->
                <div class="absolute -top-32 -right-32 w-96 h-96 bg-sky-500/10 rounded-full blur-[140px]"></div>
                <div class="absolute top-1/2 -left-40 w-96 h-96 bg-navy/25 rounded-full blur-[130px]"></div>
            </div>

            <!-- Hero Content: Clean Typographic Hierarchy (Minimalist & Semantic) -->
            <div class="relative z-10 w-full max-w-xl sm:ml-24 md:ml-16 pt-8 sm:pt-12 flex flex-col gap-6 transition-all duration-300">
                
                <!-- Eyebrow -->
                <div class="flex items-center">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 backdrop-blur-sm">
                        <span class="text-[11px] font-mono font-semibold tracking-wider text-gray-300 uppercase">
                            MYCASH
                        </span>
                    </div>
                </div>

                <!-- Primary Semantic Headline with Blue Accent Dots -->
                <h1 class="text-4xl sm:text-5xl font-black text-white tracking-tight leading-[1.05]">
                    Akurat<span class="text-sky-400">.</span> Cepat<span class="text-sky-400">.</span><br>
                    Transparan<span class="text-sky-400">.</span>
                </h1>

                <!-- Body Copy (PAS Framework) -->
                <p class="text-gray-300 text-sm sm:text-base leading-relaxed font-normal max-w-md">
                    Kelola kas kelas tanpa drama selisih uang atau rekap manual. Catat setoran siswa dalam hitungan detik, pantau pengeluaran terbuka, dan cetak laporan resmi langsung dari ponsel.
                </p>

                <!-- Immediate UX Call-to-Action for Mobile -->
                <div class="pt-2 flex flex-wrap items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-cip-primary text-sm py-3 px-6 shadow-md">
                            <span>Buka Dashboard</span>
                            <span class="material-symbols-outlined text-sm">arrow_outward</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-cip-primary text-sm py-3 px-6 shadow-md">
                            <span>Masuk ke Akun</span>
                            <span class="material-symbols-outlined text-sm">arrow_outward</span>
                        </a>
                        <button @click="whatsappModalOpen = true" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-300 hover:text-white px-5 py-3 rounded-full border border-white/20 active:scale-95 transition-all">
                            <span class="material-symbols-outlined text-base text-sky-400">chat</span>
                            <span>Bantuan</span>
                        </button>
                    @endauth
                </div>

            </div>

        </section>

        <!-- ─── SECTION 1 (DESKTOP): HERO & 300-FRAME HARDWARE SCROLLYTELLING PINNED SHOWCASE (>= 1024px) ─── -->
        <section id="sequence-track" class="hidden lg:block relative w-full h-[100dvh] min-h-[640px] bg-[#060D17] bg-grid-subtle overflow-hidden">
            
            <!-- 100dvh Viewport Shell -->
            <div class="h-full w-full flex flex-col justify-between overflow-hidden bg-[#060D17] select-none pt-28 pb-10 sm:pb-12 px-6 sm:px-12 lg:px-20">
                
                <!-- Background 300-Frame Hardware Canvas -->
                <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none flex items-center justify-center">
                    <canvas id="sequence-canvas" class="w-full h-full block object-contain"></canvas>

                    <!-- Dark Gradient Vignette for Maximum Contrast & Atmosphere -->
                    <div class="absolute inset-0 bg-gradient-to-b from-[#060D17]/80 via-transparent to-[#060D17] pointer-events-none"></div>
                    
                    <!-- Subtle Brand Glow Accents (Bendahara Blue) -->
                    <div class="absolute -top-32 -right-32 w-[600px] h-[600px] bg-sky-500/10 rounded-full blur-[150px] pointer-events-none"></div>
                    <div class="absolute top-1/2 -left-40 w-[500px] h-[500px] bg-navy/25 rounded-full blur-[140px] pointer-events-none"></div>
                </div>

                <!-- Minimalist Unified Hero Typography Hierarchy (Semantic <h1>) -->
                <div id="hero-text-block" class="relative z-10 w-full max-w-2xl lg:max-w-3xl sm:ml-24 md:ml-16 lg:ml-[18%] xl:ml-[22%] flex flex-col gap-5 transition-all duration-300 pointer-events-none pt-12 lg:pt-16">
                    
                    <!-- Eyebrow -->
                    <div class="flex items-center">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full backdrop-blur-sm pointer-events-auto">
                            <span class="text-[20px] font-mono font-semibold tracking-wider text-gray-300 uppercase">
                                MYCASH
                            </span>
                        </div>
                    </div>

                    <!-- Semantic High-Impact Headline with Blue Accent Dots -->
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-[1.05]">
                        Akurat<span class="text-sky-400">.</span> Cepat<span class="text-sky-400">.</span> Transparan<span class="text-sky-400">.</span>
                    </h1>

                    <!-- Clean Body Paragraph (PAS Framework) -->
                    <p class="text-gray-300 text-sm sm:text-base lg:text-lg leading-relaxed font-normal max-w-lg">
                        Kelola kas kelas tanpa drama selisih uang atau rekap manual berjam-jam. Catat setoran siswa dalam hitungan detik, pantau pengeluaran terbuka, dan cetak laporan resmi langsung dari ponsel.
                    </p>

                </div>

            </div>
        </section>

        <!-- ─── SECTION 2: WHO WE ARE (#who - Minimalist Open Layout with Photo Placeholder) ─── -->
        <section id="who" class="bg-white text-[#111111] py-24 lg:py-32 px-6 sm:px-12 lg:px-20 relative bg-grid-subtle-light">
            <div class="max-w-7xl mx-auto">
                
                <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                    
                    <!-- Left Column: High-Quality Photo / Context Placeholder Frame (No heavy card box) -->
                    <div class="lg:col-span-6">
                        <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden border border-slate-200/90 shadow-lg group bg-slate-100">
                            <img src="{{ asset('assets/Hero.png') }}" 
                                 alt="Pembukuan Kas Kelas Digital MyCash" 
                                 class="w-full h-[320px] sm:h-[420px] object-cover transition-transform duration-700 group-hover:scale-105 select-none">
                        </div>
                    </div>

                    <!-- Right Column: Editorial Copywriting & Open Data Metrics (No card containers) -->
                    <div class="lg:col-span-6 flex flex-col justify-center">
                        <span class="text-xs font-mono font-bold uppercase tracking-widest text-navy mb-3 block">MENGAPA MYCASH</span>
                        <h2 class="text-3xl sm:text-4xl lg:text-[2.5rem] font-black leading-tight tracking-tight text-slate-900 mb-5">
                            Tidak ada lagi buku kas basah, nota hilang, atau selisih uang.
                        </h2>
                        <p class="text-slate-600 text-sm sm:text-base leading-relaxed mb-8">
                            Mencatat iuran di buku tulis sering menimbulkan masalah: catatan robek terkena air, nota belanja tercecer, hingga salah hitung yang memicu prasangka antar siswa. MyCash hadir sebagai standar baru pembukuan kelas—memastikan setiap rupiah tercatat rapi, riwayatnya jelas, dan dapat dipantau bersama oleh bendahara, wali kelas, serta siswa.
                        </p>
                        <div class="mt-8">
                            <a href="#what" class="inline-flex items-center gap-2 text-sm font-bold text-navy hover:text-navy-light transition-colors group">
                                <span>Lihat Fitur Lengkap</span>
                                <span class="material-symbols-outlined text-base text-blue-600 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- ─── SECTION 3: WHAT WE DO / CAPABILITIES (#what - Sleek List & Application Showcase) ─── -->
        <section id="what" class="bg-[#F8FAFC] text-[#111111] py-24 lg:py-32 px-6 sm:px-12 lg:px-20 border-t border-slate-200/80 bg-grid-subtle-light">
            <div class="max-w-7xl mx-auto">
                
                <!-- Section Header -->
                <div class="max-w-xl mb-14">
                    <span class="text-xs font-mono font-bold uppercase tracking-widest text-navy block mb-3">Apa itu mycash?</span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight text-slate-900 mb-4">
                        Semua kebutuhan kas kelas, beres di satu tempat
                    </h2>
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                        Mulai dari catat setoran harian, verifikasi pengeluaran berkala, hingga cetak rekap kas resmi di akhir semester.
                    </p>
                </div>

                <!-- Clean Editorial Feature List & Authentic Preview Frame (No heavy card boxes on list) -->
                <div class="grid lg:grid-cols-12 gap-8 lg:gap-14 items-start">
                    
                    <!-- Left: Clean Divide-Y Feature List (Zero box cards) -->
                    <div class="lg:col-span-5 space-y-1 divide-y divide-slate-200">
                        <template x-for="(tab, index) in tabs" :key="index">
                            <div @click="activeTab = index"
                                 class="py-5 cursor-pointer transition-colors duration-150 group">
                                
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="font-mono text-xs font-bold transition-colors"
                                              :class="activeTab === index ? 'text-navy' : 'text-slate-400 group-hover:text-slate-600'"
                                              x-text="`0${index + 1}`"></span>
                                        <h3 class="text-base sm:text-lg font-bold transition-colors"
                                            :class="activeTab === index ? 'text-navy font-black' : 'text-slate-700 group-hover:text-slate-900'"
                                            x-text="tab.title"></h3>
                                    </div>
                                    <span class="material-symbols-outlined text-base transition-transform"
                                          :class="activeTab === index ? 'text-navy translate-x-1' : 'text-slate-300 group-hover:text-slate-500'">arrow_forward</span>
                                </div>

                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mt-2 pl-7" 
                                   x-show="activeTab === index" 
                                   x-transition:enter="transition ease-out duration-150"
                                   x-transition:enter-start="opacity-0 -translate-y-1"
                                   x-transition:enter-end="opacity-100 translate-y-0"
                                   x-text="tab.description"></p>
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </section>

        <!-- ─── SECTION 4: WHY US (#why-us - Architectural Minimalist Open Grid) ─── -->
        <section id="why-us" class="bg-[#1B4F72] text-white py-24 lg:py-32 px-6 sm:px-12 lg:px-20 border-t border-white/10 bg-grid-subtle">
            <div class="max-w-7xl mx-auto">
                
                <div class="grid lg:grid-cols-12 gap-10 items-start mb-16">
                    <div class="lg:col-span-4">
                        <span class="text-xs font-mono font-bold uppercase tracking-widest text-sky-400 block mb-2">KENAPA MYCASH?</span>
                        <span class="text-sm font-medium text-slate-400">Standar Baru Kas Sekolah</span>
                    </div>
                    <div class="lg:col-span-8">
                        <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight mb-4">
                            Dibuat khusus agar tidak ada lagi uang kas yang selisih atau dicurigai.
                        </h2>
                        <p class="text-slate-400 text-base sm:text-lg leading-relaxed max-w-2xl">
                            Dirancang untuk kebutuhan sekolah: menjaga integritas bendahara, memudahkan pengawasan wali kelas, dan memberikan kepastian kepada seluruh siswa.
                        </p>
                    </div>
                </div>

                <!-- 3 Minimalist Open Columns (Whitespace & subtle top hairlines) -->
                <div class="grid md:grid-cols-3 gap-10 lg:gap-14">
                    
                    <!-- Column 1 -->
                    <div class="border-t border-white/15 pt-8">
                        <div class="text-sm font-mono font-bold text-sky-400 mb-4 tracking-wider">01 — INTEGRITAS AUDIT</div>
                        <h3 class="text-2xl font-black mb-3 text-white tracking-tight">Jejak Perubahan Terbuka.</h3>
                        <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                            Setiap pemasukan dan pengeluaran tercatat rapi secara kronologis. Bila ada transaksi yang dikoreksi atau dibatalkan, alasannya tersimpan otomatis dalam audit trail tanpa bisa disembunyikan.
                        </p>
                    </div>

                    <!-- Column 2 -->
                    <div class="border-t border-white/15 pt-8">
                        <div class="text-sm font-mono font-bold text-sky-400 mb-4 tracking-wider">02 — AKSES FLEKSIBEL</div>
                        <h3 class="text-2xl font-black mb-3 text-white tracking-tight">Praktis di HP &amp; Komputer.</h3>
                        <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                            Bendahara cukup membuka browser ponsel saat keliling kelas. Centang nama siswa yang membayar, status tagihan langsung terupdate lunas seketika tanpa perlu laptop.
                        </p>
                    </div>

                    <!-- Column 3 -->
                    <div class="border-t border-white/15 pt-8">
                        <div class="text-sm font-mono font-bold text-sky-400 mb-4 tracking-wider">03 — LAPORAN RESMI</div>
                        <h3 class="text-2xl font-black mb-3 text-white tracking-tight">Ekspor Siap Serah &amp; Cetak.</h3>
                        <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                            Tidak perlu lembur membuat tabel di Excel dari nol. Sekali klik, laporan kas bulanan atau semesteran langsung terbit dalam format Excel atau PDF siap ditandatangani.
                        </p>
                    </div>

                </div>

            </div>
        </section>

        <!-- ─── SECTION 5: FAQ ACCORDION (#faq - Clean Divide-Y Minimalist Style, No Cards) ─── -->
        <section id="faq" class="bg-white text-[#111111] py-24 lg:py-32 px-6 sm:px-12 lg:px-20 border-t border-slate-200 bg-grid-subtle-light">
            <div class="max-w-4xl mx-auto">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="text-xs font-mono font-bold uppercase tracking-widest text-navy block mb-3">PERTANYAAN UMUM</span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight text-slate-900 mb-4">
                        Pertanyaan seputar MyCash
                    </h2>
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                        Segala hal yang sering ditanyakan tentang penggunaan aplikasi pembukuan kas kelas digital MyCash.
                    </p>
                </div>

                <!-- Clean Divide-Y Accordion List (Zero box cards) -->
                <div class="divide-y divide-slate-200 border-y border-slate-200">
                    <!-- FAQ 1 -->
                    <div class="py-5">
                        <button @click="activeFaq = (activeFaq === 1 ? null : 1)"
                                class="w-full text-left flex items-center justify-between gap-4 font-bold text-base sm:text-lg text-slate-900 hover:text-navy transition-colors">
                            <span>Apakah MyCash bisa digunakan di HP tanpa perlu instal aplikasi?</span>
                            <span class="material-symbols-outlined text-slate-400 transition-transform duration-200 flex-shrink-0"
                                  :class="activeFaq === 1 ? 'rotate-180 text-navy' : ''">expand_more</span>
                        </button>
                        <div x-show="activeFaq === 1" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="pt-3 pb-2 text-sm text-slate-600 leading-relaxed max-w-3xl">
                            Ya, tentu saja. MyCash dibangun dengan arsitektur web responsif sehingga dapat langsung diakses melalui browser smartphone apa pun (Google Chrome, Safari, Samsung Internet) tanpa wajib menginstal aplikasi. Selain itu, kami juga menyediakan aplikasi Android bagi bendahara yang menginginkan akses instan dari layar utama ponsel.
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="py-5">
                        <button @click="activeFaq = (activeFaq === 2 ? null : 2)"
                                class="w-full text-left flex items-center justify-between gap-4 font-bold text-base sm:text-lg text-slate-900 hover:text-navy transition-colors">
                            <span>Bagaimana jika bendahara salah memasukkan nominal kas?</span>
                            <span class="material-symbols-outlined text-slate-400 transition-transform duration-200 flex-shrink-0"
                                  :class="activeFaq === 2 ? 'rotate-180 text-navy' : ''">expand_more</span>
                        </button>
                        <div x-show="activeFaq === 2" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="pt-3 pb-2 text-sm text-slate-600 leading-relaxed max-w-3xl">
                            MyCash dirancang dengan sistem Audit Trail terintegrasi. Jika ada kesalahan input nominal atau data transaksi ganda, bendahara dapat melakukan koreksi atau pembatalan transaksi dengan menyertakan alasan. Seluruh riwayat perubahan akan terekam otomatis beserta waktu dan identitas akun agar pembukuan tetap transparan.
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="py-5">
                        <button @click="activeFaq = (activeFaq === 3 ? null : 3)"
                                class="w-full text-left flex items-center justify-between gap-4 font-bold text-base sm:text-lg text-slate-900 hover:text-navy transition-colors">
                            <span>Apakah siswa atau wali kelas bisa melihat rincian kas?</span>
                            <span class="material-symbols-outlined text-slate-400 transition-transform duration-200 flex-shrink-0"
                                  :class="activeFaq === 3 ? 'rotate-180 text-navy' : ''">expand_more</span>
                        </button>
                        <div x-show="activeFaq === 3" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="pt-3 pb-2 text-sm text-slate-600 leading-relaxed max-w-3xl">
                            Bisa! MyCash menerapkan sistem pembagian peran (Role-Based Access). Siswa dan wali kelas memiliki akun khusus untuk memantau status pembayaran, saldo kas berjalan, dan rincian pengeluaran secara transparan tanpa risiko data kas terubah atau terhapus secara sengaja maupun tidak sengaja.
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="py-5">
                        <button @click="activeFaq = (activeFaq === 4 ? null : 4)"
                                class="w-full text-left flex items-center justify-between gap-4 font-bold text-base sm:text-lg text-slate-900 hover:text-navy transition-colors">
                            <span>Format apa saja yang didukung untuk unduh laporan?</span>
                            <span class="material-symbols-outlined text-slate-400 transition-transform duration-200 flex-shrink-0"
                                  :class="activeFaq === 4 ? 'rotate-180 text-navy' : ''">expand_more</span>
                        </button>
                        <div x-show="activeFaq === 4" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="pt-3 pb-2 text-sm text-slate-600 leading-relaxed max-w-3xl">
                            Laporan kas dapat diunduh sekali klik ke dalam file Microsoft Excel (.xlsx) untuk rekapan arsip data, maupun diekspor langsung ke format PDF standar resmi yang dilengkapi kop kelas, ringkasan saldo awal/akhir, serta kolom tanda tangan bendahara dan wali kelas.
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="py-5">
                        <button @click="activeFaq = (activeFaq === 5 ? null : 5)"
                                class="w-full text-left flex items-center justify-between gap-4 font-bold text-base sm:text-lg text-slate-900 hover:text-navy transition-colors">
                            <span>Apakah data kas kelas aman jika HP bendahara hilang atau rusak?</span>
                            <span class="material-symbols-outlined text-slate-400 transition-transform duration-200 flex-shrink-0"
                                  :class="activeFaq === 5 ? 'rotate-180 text-navy' : ''">expand_more</span>
                        </button>
                        <div x-show="activeFaq === 5" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="pt-3 pb-2 text-sm text-slate-600 leading-relaxed max-w-3xl">
                            Sangat aman. Berbeda dengan buku tulis yang rentan hilang atau rusak terkena air, seluruh data kas MyCash tersimpan di cloud database yang aman dengan pencadangan berkala. Jika ganti HP atau perangkat, Anda tinggal login kembali dengan akun terdaftar dan semua data tetap utuh.
                        </div>
                    </div>
                </div>

                <div class="mt-12 text-center">
                    <p class="text-sm text-slate-500">
                        Punya pertanyaan lain seputar implementasi di kelas Anda? 
                        <button @click="whatsappModalOpen = true" class="text-navy font-bold hover:text-navy-light transition-colors inline-flex items-center gap-1 ml-1">
                            <span>Hubungi tim bantuan via WhatsApp</span>
                            <span class="material-symbols-outlined text-sm">arrow_outward</span>
                        </button>
                    </p>
                </div>
            </div>
        </section>

        <!-- ─── SECTION 6: CTA SECTION (Minimalist Direct Action) ─── -->
        <section class="bg-[#1B4F72] text-white py-24 lg:py-32 px-6 sm:px-12 lg:px-20 relative text-center border-t border-white/10 bg-grid-subtle">
            <div class="max-w-3xl mx-auto">
                <span class="text-xs font-mono font-bold uppercase tracking-widest text-sky-400 block mb-4">MULAI SEKARANG</span>
                <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight mb-6">
                    Siap kelola kas kelas lebih rapi, akurat, dan transparan?
                </h2>
                <p class="text-slate-400 text-base sm:text-lg leading-relaxed max-w-xl mx-auto mb-10">
                    Masuk ke MyCash dan rasakan kemudahan catat iuran kelas tanpa drama selisih uang atau rekap manual berjam-jam.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-cip-primary text-base py-3.5 px-8">
                            <span>Buka Dashboard</span>
                            <span class="material-symbols-outlined text-base">arrow_outward</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-cip-primary text-base py-3.5 px-8">
                            <span>Masuk ke Akun</span>
                            <span class="material-symbols-outlined text-base">arrow_outward</span>
                        </a>
                        <button @click="whatsappModalOpen = true" class="inline-flex items-center gap-2 text-sm font-bold text-slate-300 hover:text-white px-6 py-3.5 rounded-full border border-white/20 hover:border-sky-400/50 transition-colors">
                            <span class="material-symbols-outlined text-base text-sky-400">chat</span>
                            <span>Konsultasi WhatsApp</span>
                        </button>
                    @endauth
                </div>
            </div>
        </section>

        <!-- ─── FOOTER SECTION (Minimalist 4-Column Grid) ─── -->
        <footer class="bg-white text-[#111111] pt-20 pb-28 px-6 sm:px-12 lg:px-20 border-t border-slate-200">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-12">
                
                <!-- Col 1: Brand & Details -->
                <div class="md:col-span-5">
                    <a href="/" class="text-3xl font-black tracking-tight text-[#111111] flex items-center gap-2.5 mb-6 group">
                        <img src="{{ asset('assets/logo-mycash.png') }}" alt="Logo MyCash" class="w-8 h-8 object-contain">
                        <span>mycash<span class="text-navy text-4xl leading-none">.</span></span>
                    </a>
                    <div class="text-xs text-slate-500 space-y-2 font-medium max-w-sm">
                        <p class="font-bold text-navy text-sm">Aplikasi Kas Kelas &amp; Iuran Sekolah Digital</p>
                        <p class="leading-relaxed">Solusi pembukuan transparan untuk bendahara, wali kelas, dan siswa. Menghilangkan selisih kas dan mempermudah pelaporan keuangan.</p>
                        <p class="pt-4 font-mono text-slate-400">© {{ date('Y') }} MyCash. Semua hak cipta dilindungi.</p>
                    </div>
                </div>

                <!-- Col 2: Navigation Links -->
                <div class="md:col-span-2 text-xs space-y-3 font-semibold text-slate-600">
                    <span class="font-bold text-navy uppercase tracking-wider block mb-4 text-[11px] font-mono">Navigasi</span>
                    <div><a href="#sequence-track" class="hover:text-navy transition-colors">Beranda</a></div>
                    <div><a href="#who" class="hover:text-navy transition-colors">Siapa Kami</a></div>
                    <div><a href="#what" class="hover:text-navy transition-colors">Fitur Aplikasi</a></div>
                    <div><a href="#why-us" class="hover:text-navy transition-colors">Keunggulan</a></div>
                    <div><a href="#faq" class="hover:text-navy transition-colors">FAQ</a></div>
                </div>

                <!-- Col 3: Fitur Utama -->
                <div class="md:col-span-3 text-xs space-y-3 font-semibold text-slate-600">
                    <span class="font-bold text-navy uppercase tracking-wider block mb-4 text-[11px] font-mono">Fitur Utama</span>
                    <div><span class="text-slate-500">Checklist Kas Siswa</span></div>
                    <div><span class="text-slate-500">Audit Trail Transparan</span></div>
                    <div><span class="text-slate-500">Ekspor Laporan PDF &amp; Excel</span></div>
                    <div><span class="text-slate-500">Akses Khusus Tiap Peran</span></div>
                </div>

                <!-- Col 4: Portal & Support -->
                <div class="md:col-span-2 text-xs space-y-3 font-semibold text-slate-600">
                    <span class="font-bold text-navy uppercase tracking-wider block mb-4 text-[11px] font-mono">Portal &amp; Akses</span>
                    <div><a href="{{ route('login') }}" class="hover:text-navy transition-colors">Masuk Bendahara</a></div>
                    <div><a href="{{ route('login') }}" class="hover:text-navy transition-colors">Masuk Siswa</a></div>
                    <div><button @click="whatsappModalOpen = true" class="hover:text-navy transition-colors text-left">Bantuan WhatsApp</button></div>
                </div>

            </div>
        </footer>
    </main>

    <!-- ─── WHATSAPP / CONTACT MODAL (Bendahara Blue Styling) ─── -->
    <div x-show="whatsappModalOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 backdrop-blur-none"
         x-transition:enter-end="opacity-100 backdrop-blur-sm"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 backdrop-blur-sm"
         x-transition:leave-end="opacity-0 backdrop-blur-none"
         @click.self="whatsappModalOpen = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-5 bg-black/60"
         style="display:none;">
        
        <div class="w-full max-w-sm bg-white text-[#111111] rounded-3xl p-6 shadow-2xl relative border border-slate-100">
            <!-- Close button -->
            <button @click="whatsappModalOpen = false" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>

            <div class="text-center py-2">
                <div class="w-14 h-14 rounded-full bg-blue-50 text-navy flex items-center justify-center mx-auto mb-4 border border-blue-200/60">
                    <span class="material-symbols-outlined text-3xl">chat</span>
                </div>
                <h3 class="text-xl font-bold tracking-tight text-navy">Hubungi Kami di WhatsApp</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Punya pertanyaan tentang penerapan MyCash di sekolah atau kelas Anda? Tim kami siap membantu.
                </p>

                <div class="mt-6">
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin%20MyCash,%20saya%20tertarik%20menggunakan%20aplikasi%20ini%20untuk%20kas%20kelas" 
                       target="_blank"
                       @click="whatsappModalOpen = false"
                       class="w-full py-3 px-6 rounded-full bg-navy hover:bg-navy-dark text-white font-bold text-sm shadow-lg shadow-navy/25 flex items-center justify-center gap-2 transition-all">
                        <span>Buka Chat WhatsApp</span>
                        <span class="material-symbols-outlined text-sm text-sky-400">arrow_outward</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>