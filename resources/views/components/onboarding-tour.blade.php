@php
    $role = 'siswa';
    if (Auth::user()->hasRole('admin')) {
        $role = 'admin';
    } elseif (Auth::user()->hasRole('bendahara')) {
        $role = 'bendahara';
    } elseif (Auth::user()->hasRole('wali_kelas')) {
        $role = 'wali_kelas';
    }

    $tourConfig = [
        'bendahara' => [
            [
                'url' => route('bendahara.dashboard'),
                'selector' => '#tour-bendahara-stat-saldo',
                'pageName' => 'Dashboard',
                'title' => 'Saldo Kas Kelas',
                'side' => 'bottom',
                'content' => 'Pantau sisa saldo kas, total pemasukan, dan pengeluaran kelas saat ini.'
            ],
            [
                'url' => route('bendahara.transactions.index'),
                'selector' => '#tour-bendahara-btn-create-tx',
                'pageName' => 'Transaksi Kas',
                'title' => 'Catat Kas Masuk',
                'side' => 'left',
                'content' => 'Klik tombol ini untuk mencatat uang masuk atau pengeluaran baru.'
            ],
            [
                'url' => route('bendahara.transactions.create'),
                'selector' => '#tour-bendahara-tx-type',
                'pageName' => 'Form Catat Transaksi',
                'title' => 'Tipe Transaksi',
                'side' => 'bottom',
                'content' => 'Pilih Pemasukan untuk uang masuk, atau Pengeluaran untuk belanja kas.'
            ],
            [
                'url' => route('bendahara.transactions.create'),
                'selector' => '#tour-bendahara-tx-category',
                'pageName' => 'Form Catat Transaksi',
                'title' => 'Kategori Transaksi',
                'side' => 'bottom',
                'content' => 'Pilih kategori kas yang sesuai, misalnya Uang Kas Rutin atau Kebutuhan Kelas.'
            ],
            [
                'url' => route('bendahara.transactions.create'),
                'selector' => '#tour-bendahara-form-student',
                'pageName' => 'Form Catat Transaksi',
                'title' => 'Nama Siswa',
                'side' => 'bottom',
                'content' => 'Pilih siswa yang membayar. Checklist kas siswa akan otomatis tercentang lunas.'
            ],
            [
                'url' => route('bendahara.transactions.create'),
                'selector' => '#tour-bendahara-tx-amount',
                'pageName' => 'Form Catat Transaksi',
                'title' => 'Jumlah (Rp)',
                'side' => 'bottom',
                'content' => 'Ketik jumlah uang yang disetor atau dibelanjakan.'
            ],
            [
                'url' => route('bendahara.transactions.create'),
                'selector' => '#tour-bendahara-tx-description',
                'pageName' => 'Form Catat Transaksi',
                'title' => 'Deskripsi Transaksi',
                'side' => 'bottom',
                'content' => 'Tulis catatan singkat agar pembukuan kas tetap rapi dan jelas.'
            ],
            [
                'url' => route('bendahara.transactions.create'),
                'selector' => '#tour-bendahara-tx-date',
                'pageName' => 'Form Catat Transaksi',
                'title' => 'Tanggal Transaksi',
                'side' => 'top',
                'content' => 'Pilih tanggal transaksi (default sudah terisi hari ini).'
            ],
            [
                'url' => route('bendahara.students.index'),
                'selector' => '#tour-bendahara-btn-settings',
                'pageName' => 'Checklist Kas',
                'title' => 'Pengaturan Kas',
                'side' => 'bottom',
                'content' => 'Atur periode kas (bulanan atau mingguan) dan nominal iuran per periode.'
            ],
            [
                'url' => route('bendahara.students.index'),
                'selector' => '#tour-bendahara-students-table',
                'pageName' => 'Checklist Kas',
                'title' => 'Aturan Kas Saat Ini',
                'side' => 'bottom',
                'content' => 'Ringkasan periode dan nominal iuran yang sedang berlaku di kelas ini.'
            ],
            [
                'url' => route('bendahara.students.index'),
                'selector' => '#tour-bendahara-checklist-matrix',
                'mobileSelector' => '#tour-bendahara-checklist-mobile',
                'pageName' => 'Checklist Kas',
                'title' => 'Daftar Pembayaran Kas Siswa',
                'side' => 'top',
                'content' => 'Tabel status pembayaran kas untuk seluruh siswa di kelas.'
            ],
            [
                'url' => route('bendahara.students.index'),
                'selector' => '#tour-bendahara-col-periods',
                'mobileSelector' => '#tour-bendahara-col-periods-mobile',
                'pageName' => 'Checklist Kas',
                'title' => 'Centang Otomatis',
                'side' => 'bottom',
                'content' => 'Centang lunas otomatis terisi saat pembayaran kas siswa dicatat.'
            ],
            [
                'url' => route('bendahara.students.index'),
                'selector' => '#tour-bendahara-col-paid',
                'mobileSelector' => '#tour-bendahara-col-paid-mobile',
                'pageName' => 'Checklist Kas',
                'title' => 'Total Dibayar',
                'side' => 'bottom',
                'align' => 'end',
                'content' => 'Total uang kas yang sudah dibayarkan oleh masing-masing siswa.'
            ],
            [
                'url' => route('bendahara.students.index'),
                'selector' => '#tour-bendahara-col-debt',
                'mobileSelector' => '#tour-bendahara-col-debt-mobile',
                'pageName' => 'Checklist Kas',
                'title' => 'Sisa Tunggakan',
                'side' => 'bottom',
                'align' => 'end',
                'content' => 'Sisa kas yang belum dibayar oleh siswa.'
            ],
            [
                'url' => route('bendahara.report.index'),
                'selector' => '#tour-bendahara-report-filter',
                'pageName' => 'Laporan Kas',
                'title' => 'Filter Tanggal & Kategori',
                'side' => 'bottom',
                'content' => 'Pilih rentang tanggal dan kategori kas yang ingin ditampilkan.'
            ],
            [
                'url' => route('bendahara.report.index'),
                'selector' => '#tour-bendahara-report-stats',
                'pageName' => 'Laporan Kas',
                'title' => 'Ringkasan Saldo Kas',
                'side' => 'bottom',
                'content' => 'Cek saldo awal, uang masuk, pengeluaran, dan sisa saldo akhir pada periode ini.'
            ],
            [
                'url' => route('bendahara.report.index'),
                'selector' => '#tour-bendahara-report-chart',
                'pageName' => 'Laporan Kas',
                'title' => 'Grafik Kas Masuk & Keluar',
                'side' => 'top',
                'content' => 'Grafik perbandingan uang masuk dan pengeluaran kas setiap bulan.'
            ],
            [
                'url' => route('bendahara.report.index'),
                'selector' => '#tour-bendahara-report-table',
                'pageName' => 'Laporan Kas',
                'title' => 'Riwayat Mutasi Kas',
                'side' => 'top',
                'content' => 'Daftar lengkap transaksi kas berurutan beserta hitungan saldo berjalannya.'
            ],
            [
                'url' => route('bendahara.report.index'),
                'selector' => '#tour-bendahara-report-actions',
                'pageName' => 'Laporan Kas',
                'title' => 'Ekspor Excel & Cetak PDF',
                'side' => 'bottom',
                'align' => 'end',
                'content' => 'Unduh laporan kas ke file Excel atau cetak dokumen PDF untuk arsip atau diserahkan ke Wali Kelas.'
            ],
            [
                'url' => route('bendahara.announcements.index'),
                'selector' => '#tour-bendahara-announcement-stats',
                'pageName' => 'Pengumuman Kas',
                'title' => 'Ringkasan Pengumuman',
                'side' => 'bottom',
                'content' => 'Lihat jumlah siswa di kelas dan total pengumuman kas yang sudah dibagikan.'
            ],
            [
                'url' => route('bendahara.announcements.index'),
                'selector' => '#tour-bendahara-announcement-list',
                'pageName' => 'Pengumuman Kas',
                'title' => 'Daftar Pengumuman',
                'side' => 'top',
                'content' => 'Lihat riwayat pengumuman yang dikirim dan berapa banyak siswa yang sudah membacanya.'
            ],
            [
                'url' => route('bendahara.announcements.index'),
                'selector' => '#tour-bendahara-btn-create-announcement',
                'pageName' => 'Pengumuman Kas',
                'title' => 'Buat Pengumuman Baru',
                'side' => 'bottom',
                'align' => 'end',
                'content' => 'Klik tombol ini untuk membuat dan mengirim pengumuman baru ke siswa.'
            ],
            [
                'url' => route('bendahara.announcements.create'),
                'selector' => '#tour-bendahara-announcement-target',
                'pageName' => 'Form Pengumuman',
                'title' => 'Penerima Pengumuman',
                'side' => 'bottom',
                'content' => 'Pilih mau dikirim ke seluruh siswa dalam satu kelas atau hanya siswa tertentu.'
            ],
            [
                'url' => route('bendahara.announcements.create'),
                'selector' => '#tour-bendahara-announcement-title',
                'pageName' => 'Form Pengumuman',
                'title' => 'Judul Pengumuman',
                'side' => 'bottom',
                'content' => 'Tulis judul pengumuman yang jelas, misalnya Pengingat Uang Kas Bulan Ini.'
            ],
            [
                'url' => route('bendahara.announcements.create'),
                'selector' => '#tour-bendahara-announcement-body',
                'pageName' => 'Form Pengumuman',
                'title' => 'Isi Pengumuman',
                'side' => 'top',
                'content' => 'Tulis detail informasi, misalnya batas waktu setoran atau rencana penggunaan kas kelas.'
            ],
            [
                'url' => route('bendahara.announcements.create'),
                'selector' => '#tour-bendahara-announcement-submit',
                'pageName' => 'Form Pengumuman',
                'title' => 'Kirim Pengumuman',
                'side' => 'top',
                'align' => 'end',
                'content' => 'Klik tombol ini untuk langsung mengirim pengumuman ke siswa.'
            ]
        ],
        'siswa' => [
            [
                'url' => route('siswa.dashboard'),
                'selector' => '#tour-siswa-stat-card',
                'pageName' => 'Dashboard',
                'title' => 'Total Kas Terbayar',
                'side' => 'bottom',
                'content' => 'Jumlah uang kas yang sudah Anda setorkan ke bendahara kelas.'
            ],
            [
                'url' => route('siswa.history.index'),
                'selector' => '#tour-siswa-history-matrix',
                'pageName' => 'Riwayat Kas',
                'title' => 'Status Pembayaran',
                'side' => 'bottom',
                'content' => 'Tanda centang lunas akan otomatis muncul setelah setoran dicatat oleh bendahara.'
            ],
            [
                'url' => route('siswa.notifications.index'),
                'selector' => '#tour-siswa-notifications-list',
                'pageName' => 'Pengumuman Kas',
                'title' => 'Pengumuman & Notifikasi',
                'side' => 'bottom',
                'content' => 'Lihat kabar, info kas, atau pengingat setoran dari bendahara kelas Anda.'
            ]
        ],
        'wali_kelas' => [
            [
                'url' => route('wali-kelas.dashboard'),
                'selector' => '#tour-wali-stats',
                'pageName' => 'Dashboard',
                'title' => 'Ringkasan Kas Kelas',
                'side' => 'bottom',
                'content' => 'Pantau saldo kas dan catatan pengeluaran kelas yang Anda ampu.'
            ],
            [
                'url' => route('wali-kelas.students.index'),
                'selector' => '#tour-wali-students-table',
                'pageName' => 'Data Siswa',
                'title' => 'Status Pembayaran Siswa',
                'side' => 'bottom',
                'content' => 'Cek daftar siswa untuk melihat siapa yang sudah lunas dan siapa yang masih memiliki tunggakan.'
            ],
            [
                'url' => route('wali-kelas.report.index'),
                'selector' => '#tour-wali-report-filter',
                'pageName' => 'Laporan Kas',
                'title' => 'Filter Tanggal & Kategori',
                'side' => 'bottom',
                'content' => 'Saring catatan kas berdasarkan periode tanggal dan kategori yang diinginkan.'
            ],
            [
                'url' => route('wali-kelas.report.index'),
                'selector' => '#tour-wali-report-stats',
                'pageName' => 'Laporan Kas',
                'title' => 'Ringkasan Kas Kelas',
                'side' => 'bottom',
                'content' => 'Lihat saldo awal, total kas masuk, pengeluaran, dan saldo akhir pada periode ini.'
            ],
            [
                'url' => route('wali-kelas.report.index'),
                'selector' => '#tour-wali-report-chart',
                'pageName' => 'Laporan Kas',
                'title' => 'Grafik Kas Bulanan',
                'side' => 'top',
                'content' => 'Grafik perbandingan uang kas masuk dan pengeluaran setiap bulan.'
            ],
            [
                'url' => route('wali-kelas.report.index'),
                'selector' => '#tour-wali-report-table',
                'pageName' => 'Laporan Kas',
                'title' => 'Riwayat Mutasi Kas',
                'side' => 'top',
                'content' => 'Daftar lengkap transaksi kas berurutan dengan saldo berjalan yang rapi.'
            ],
            [
                'url' => route('wali-kelas.report.index'),
                'selector' => '#tour-wali-report-actions',
                'pageName' => 'Laporan Kas',
                'title' => 'Unduh Rekap Kas',
                'side' => 'bottom',
                'align' => 'end',
                'content' => 'Unduh laporan kas ke format Excel atau cetak PDF untuk arsip dan rapat kelas.'
            ]
        ],
        'admin' => [
            [
                'url' => route('admin.dashboard'),
                'selector' => '#tour-admin-stats',
                'pageName' => 'Dashboard',
                'title' => 'Ringkasan Kas Seluruh Kelas',
                'side' => 'bottom',
                'content' => 'Pantau perputaran saldo kas dan transaksi dari seluruh kelas di sekolah.'
            ],
            [
                'url' => route('admin.kelas.index'),
                'selector' => '#tour-admin-kelas-table',
                'pageName' => 'Kelola Kelas',
                'title' => 'Kelola Data Kelas',
                'side' => 'bottom',
                'content' => 'Atur data kelas, besaran iuran kas, serta tentukan bendahara dan wali kelasnya.'
            ]
        ]
    ];

    $stepsJson = json_encode($tourConfig[$role] ?? $tourConfig['siswa']);
@endphp

<!-- Driver.js CSS (Local Vendor) -->
<link rel="stylesheet" href="{{ asset('vendor/driverjs/driver.css') }}"/>

<!-- Onboarding Tour Component (Alpine + Driver.js Integration) -->
<div x-data="onboardingTour('{{ $role }}', {{ $stepsJson }})"
     x-cloak
     class="hidden"
     style="display: none;">
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('onboardingTour', (userRole, configSteps) => ({
        role: userRole,
        steps: configSteps || [],
        currentStep: 0,
        driverInstance: null,

        init() {
            this.ensureDriverLoaded(() => {
                this.handleAutoOrPersistedState();
            });

            // Global listener to trigger from topbar help button (?) anytime
            window.addEventListener('start-tour', () => {
                const firstStep = this.steps[0];
                if (!firstStep) return;

                const currentPath = window.location.pathname;
                const firstStepPath = new URL(firstStep.url, window.location.origin).pathname;

                sessionStorage.setItem('mycash_walkthrough_state', JSON.stringify({
                    active: true,
                    role: this.role,
                    step: 0
                }));

                if (currentPath !== firstStepPath) {
                    if (this.driverInstance) {
                        try { this.driverInstance.destroy(); } catch(e) {}
                        this.driverInstance = null;
                    }
                    window.location.href = firstStep.url;
                } else {
                    this.currentStep = 0;
                    this.ensureDriverLoaded(() => {
                        this.startHighlight(0);
                    });
                }
            });
        },

        ensureDriverLoaded(callback) {
            if (typeof window.driver === 'function') {
                callback();
                return;
            }
            if (window.driver?.js?.driver) {
                window.driver = window.driver.js.driver;
                callback();
                return;
            }
            // Load from CDN fallback if not yet available
            if (!document.getElementById('driver-js-script')) {
                const script = document.createElement('script');
                script.id = 'driver-js-script';
                script.src = '{{ asset('vendor/driverjs/driver.js.iife.js') }}';
                script.onload = () => {
                    if (window.driver?.js?.driver) {
                        window.driver = window.driver.js.driver;
                    }
                    callback();
                };
                document.head.appendChild(script);
            } else {
                const checkInterval = setInterval(() => {
                    if (typeof window.driver === 'function' || window.driver?.js?.driver) {
                        clearInterval(checkInterval);
                        if (window.driver?.js?.driver) window.driver = window.driver.js.driver;
                        callback();
                    }
                }, 40);
            }
        },

        handleAutoOrPersistedState() {
            const stateStr = sessionStorage.getItem('mycash_walkthrough_state');
            const completed = localStorage.getItem('mycash_walkthrough_completed_' + this.role);

            if (stateStr) {
                try {
                    const state = JSON.parse(stateStr);
                    if (state.active && state.role === this.role) {
                        const currentPath = window.location.pathname;
                        const stepIdx = state.step || 0;
                        const step = this.steps[stepIdx];

                        if (step && currentPath === new URL(step.url, window.location.origin).pathname) {
                            this.currentStep = stepIdx;
                            this.waitForTargetAndHighlight(stepIdx);
                            return;
                        }

                        // Check if current path matches another step (e.g. user navigated to that page)
                        const matchingIdx = this.steps.findIndex(s => new URL(s.url, window.location.origin).pathname === currentPath);
                        if (matchingIdx !== -1) {
                            this.currentStep = matchingIdx;
                            state.step = matchingIdx;
                            sessionStorage.setItem('mycash_walkthrough_state', JSON.stringify(state));
                            this.waitForTargetAndHighlight(matchingIdx);
                            return;
                        }
                    }
                } catch(e) {}
            } else if (!completed) {
                // First time visit on landing dashboard
                const firstStep = this.steps[0];
                if (firstStep) {
                    const currentPath = window.location.pathname;
                    const firstStepPath = new URL(firstStep.url, window.location.origin).pathname;
                    if (currentPath === firstStepPath) {
                        this.waitForTargetAndHighlight(0, 10);
                    }
                }
            }
        },

        waitForTargetAndHighlight(stepIdx, maxRetries = 15) {
            const step = this.steps[stepIdx];
            if (!step) return;

            let retries = 0;
            const check = () => {
                const el = this.resolveTarget(step);
                if (el || retries >= maxRetries) {
                    this.startHighlight(stepIdx);
                } else {
                    retries++;
                    setTimeout(check, 100);
                }
            };
            check();
        },

        resolveTarget(step) {
            if (!step) return null;
            const isMobile = window.innerWidth < 768;

            // On mobile devices, prioritize mobileSelector if specified
            if (isMobile && step.mobileSelector) {
                const mobEl = document.querySelector(step.mobileSelector);
                if (mobEl && this.isElementVisible(mobEl)) return mobEl;
            }

            // Check primary selector
            if (step.selector) {
                const el = document.querySelector(step.selector);
                if (el && this.isElementVisible(el)) return el;
            }

            // Fallback to mobileSelector if primary is hidden/unavailable
            if (step.mobileSelector) {
                const mobEl = document.querySelector(step.mobileSelector);
                if (mobEl && this.isElementVisible(mobEl)) return mobEl;
            }

            return null;
        },

        isElementVisible(el) {
            if (!el) return false;
            const style = window.getComputedStyle(el);
            if (style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0') return false;
            const rect = el.getBoundingClientRect();
            return (rect.width > 0 && rect.height > 0);
        },

        scrollParentToTarget(el) {
            if (!el) return;
            let parent = el.parentElement;
            while (parent && parent !== document.body) {
                const style = window.getComputedStyle(parent);
                if (style.overflowX === 'auto' || style.overflowX === 'scroll') {
                    const parentRect = parent.getBoundingClientRect();
                    const elRect = el.getBoundingClientRect();
                    if (elRect.left < parentRect.left + 24 || elRect.right > parentRect.right - 24) {
                        const scrollOffset = el.offsetLeft - parent.offsetLeft - 16;
                        parent.scrollTo({
                            left: Math.max(0, scrollOffset),
                            behavior: 'smooth'
                        });
                    }
                }
                parent = parent.parentElement;
            }
        },

        getOrCreateDriver() {
            if (this.driverInstance) return this.driverInstance;

            const driverCreator = typeof window.driver === 'function' ? window.driver : (window.driver?.js?.driver || null);
            if (!driverCreator) return null;

            const driverObj = driverCreator({
                showProgress: false,
                animate: true,
                duration: 380,
                overlayColor: '#0f172a',
                overlayOpacity: 0.65,
                smoothScroll: true,
                allowClose: true,
                stagePadding: 8,
                stageRadius: 12,
                popoverClass: 'mycash-driver-popover',
                onPopoverRender: (popover) => {
                    this.adjustPopoverMobile(popover);
                },
                onDestroyStarted: () => {
                    sessionStorage.removeItem('mycash_walkthrough_state');
                    localStorage.setItem('mycash_walkthrough_completed_' + this.role, 'true');
                    this.driverInstance = null;
                    driverObj.destroy();
                }
            });

            this.driverInstance = driverObj;
            return driverObj;
        },

        adjustPopoverMobile(popover) {
            if (!popover || !popover.wrapper) return;
            const isMobile = window.innerWidth < 640;
            if (!isMobile) return;

            const el = popover.wrapper;
            requestAnimationFrame(() => {
                const rect = el.getBoundingClientRect();
                const viewportHeight = window.innerHeight;
                const padding = 12;

                // Clamp vertically inside viewport
                if (rect.bottom > viewportHeight - padding) {
                    const overflow = rect.bottom - (viewportHeight - padding);
                    const currentTop = parseFloat(el.style.top) || rect.top;
                    el.style.top = Math.max(padding, currentTop - overflow) + 'px';
                    el.style.bottom = 'auto';
                }
                if (rect.top < padding) {
                    el.style.top = padding + 'px';
                    el.style.bottom = 'auto';
                }

                // Center horizontally with clean padding on mobile
                el.style.left = padding + 'px';
                el.style.right = padding + 'px';
                el.style.width = `calc(100vw - ${padding * 2}px)`;
                el.style.maxWidth = `calc(100vw - ${padding * 2}px)`;
                el.style.boxSizing = 'border-box';
            });
        },

        startHighlight(index) {
            const step = this.steps[index];
            if (!step) return;

            this.currentStep = index;
            const isLastStep = index === this.steps.length - 1;
            const driverObj = this.getOrCreateDriver();
            if (!driverObj) return;

            const targetEl = this.resolveTarget(step);
            if (targetEl) {
                this.scrollParentToTarget(targetEl);
            }

            const isMobile = window.innerWidth < 640;

            // Responsive placement: on mobile, avoid left/right overflow
            let popoverSide = step.side || 'bottom';
            let popoverAlign = step.align || 'start';

            if (isMobile) {
                popoverAlign = 'center';
                if (targetEl) {
                    const rect = targetEl.getBoundingClientRect();
                    // If target is in the lower 55% of the screen, place popover on top
                    if (rect.top > window.innerHeight * 0.52) {
                        popoverSide = 'top';
                    } else {
                        popoverSide = 'bottom';
                    }
                } else {
                    popoverSide = 'bottom';
                }
            }

            const popoverConfig = {
                title: `<div class="flex items-center justify-between gap-2 mr-3">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block flex-shrink-0"></span>
                                <span class="font-bold text-slate-800 text-[13.5px] sm:text-[14px] leading-snug truncate">${step.title}</span>
                            </div>
                            <span class="text-[10px] sm:text-[10.5px] font-bold text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-200/70 whitespace-nowrap flex-shrink-0">
                                ${index + 1}/${this.steps.length}
                            </span>
                        </div>`,
                description: `
                    <div class="text-[12.5px] sm:text-[13px] text-slate-600 leading-relaxed mt-1.5">
                        ${step.content}
                    </div>
                `,
                side: popoverSide,
                align: popoverAlign,
                nextBtnText: isLastStep ? 'Selesai' : 'Lanjut',
                prevBtnText: index > 0 ? 'Kembali' : '',
                showButtons: index > 0 ? ['previous', 'next', 'close'] : ['next', 'close'],
                onNextClick: () => {
                    if (!isLastStep) {
                        this.goToStep(index + 1);
                    } else {
                        driverObj.destroy();
                        this.driverInstance = null;
                        sessionStorage.removeItem('mycash_walkthrough_state');
                        localStorage.setItem('mycash_walkthrough_completed_' + this.role, 'true');
                    }
                },
                onPrevClick: () => {
                    if (index > 0) {
                        this.goToStep(index - 1);
                    }
                },
                onCloseClick: () => {
                    driverObj.destroy();
                    this.driverInstance = null;
                    sessionStorage.removeItem('mycash_walkthrough_state');
                    localStorage.setItem('mycash_walkthrough_completed_' + this.role, 'true');
                }
            };

            if (targetEl) {
                driverObj.highlight({
                    element: targetEl,
                    popover: popoverConfig
                });
            } else {
                // Fallback centered popover if target element is not found
                driverObj.highlight({
                    popover: popoverConfig
                });
            }
        },

        goToStep(index) {
            const step = this.steps[index];
            if (!step) return;

            const currentPath = window.location.pathname;
            const targetPath = new URL(step.url, window.location.origin).pathname;

            sessionStorage.setItem('mycash_walkthrough_state', JSON.stringify({
                active: true,
                role: this.role,
                step: index
            }));

            if (currentPath !== targetPath) {
                // Cleanly destroy driver when leaving page
                if (this.driverInstance) {
                    try { this.driverInstance.destroy(); } catch(e) {}
                    this.driverInstance = null;
                }
                window.location.href = step.url;
            } else {
                // Same page: keep driver instance alive for silky smooth stage and popover motion!
                this.currentStep = index;
                this.startHighlight(index);
            }
        }
    }));
});
</script>

<style>
/* ─── Driver.js Modern, Fluid & Responsive MyCash Tour Styling ─── */
.driver-popover.mycash-driver-popover {
    background: #ffffff !important;
    border-radius: 16px !important;
    border: 1.5px solid #5DCAA5 !important;
    box-shadow: 0 16px 36px -6px rgba(15, 23, 42, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.04) !important;
    padding: 14px 16px !important;
    max-width: 320px !important;
    min-width: 270px !important;
    font-family: 'Work Sans', sans-serif !important;
    color: #1e293b !important;
    z-index: 100000 !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
    transform: translateZ(0) !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    animation: mycash-popover-in 0.28s cubic-bezier(0.16, 1, 0.3, 1) !important;
    transition: top 0.32s cubic-bezier(0.16, 1, 0.3, 1), left 0.32s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

@keyframes mycash-popover-in {
    0% {
        opacity: 0;
        transform: scale(0.96) translateY(5px);
    }
    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@media (max-width: 640px) {
    .driver-popover.mycash-driver-popover {
        position: fixed !important;
        left: 12px !important;
        right: 12px !important;
        width: calc(100vw - 24px) !important;
        max-width: calc(100vw - 24px) !important;
        min-width: 0 !important;
        padding: 12px 14px !important;
        margin: 0 auto !important;
        border-radius: 14px !important;
    }
    /* Hide arrow on mobile full-width floating card for clean bottom-sheet style */
    .driver-popover.mycash-driver-popover .driver-popover-arrow {
        display: none !important;
    }
}

.mycash-driver-popover .driver-popover-title {
    font-family: 'Manrope', sans-serif !important;
    font-size: 0.9375rem !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    line-height: 1.3 !important;
    margin-bottom: 2px !important;
    padding-right: 18px !important;
}

.mycash-driver-popover .driver-popover-description {
    font-size: 0.8125rem !important;
    line-height: 1.55 !important;
    color: #475569 !important;
    margin-bottom: 10px !important;
}

.mycash-driver-popover .driver-popover-footer {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    margin-top: 10px !important;
    padding-top: 10px !important;
    border-top: 1px solid #f1f5f9 !important;
    gap: 8px !important;
}

.mycash-driver-popover .driver-popover-progress-text {
    font-size: 0.7rem !important;
    font-weight: 700 !important;
    color: #0f766e !important;
    background: #f0fdfa !important;
    padding: 2px 8px !important;
    border-radius: 9999px !important;
    border: 1px solid #ccfbf1 !important;
}

.mycash-driver-popover .driver-popover-navigation-btns {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
}

.mycash-driver-popover .driver-popover-next-btn {
    font-family: 'Manrope', system-ui, -apple-system, sans-serif !important;
    background: #1B4F72 !important;
    color: #ffffff !important;
    border-radius: 9px !important;
    font-size: 0.8125rem !important;
    font-weight: 600 !important;
    min-height: 36px !important;
    line-height: 1 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 8px 18px !important;
    border: none !important;
    outline: none !important;
    box-shadow: 0 2px 6px rgba(27, 79, 114, 0.25) !important;
    transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease !important;
    cursor: pointer !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    text-rendering: optimizeLegibility !important;
}

.mycash-driver-popover .driver-popover-next-btn:hover {
    background: #153e5b !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 10px rgba(27, 79, 114, 0.35) !important;
}

.mycash-driver-popover .driver-popover-prev-btn:empty {
    display: none !important;
}

.mycash-driver-popover .driver-popover-prev-btn {
    font-family: 'Manrope', system-ui, -apple-system, sans-serif !important;
    background: #ffffff !important;
    color: #64748b !important;
    border-radius: 9px !important;
    font-size: 0.8125rem !important;
    font-weight: 600 !important;
    min-height: 36px !important;
    line-height: 1 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 8px 14px !important;
    border: 1px solid #e2e8f0 !important;
    outline: none !important;
    box-shadow: none !important;
    transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease !important;
    cursor: pointer !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    text-rendering: optimizeLegibility !important;
}

.mycash-driver-popover .driver-popover-prev-btn:hover {
    background: #f8fafc !important;
    color: #1B4F72 !important;
    border-color: #cbd5e1 !important;
}

.mycash-driver-popover .driver-popover-close-btn {
    color: #94a3b8 !important;
    font-size: 1.15rem !important;
    transition: color 0.2s !important;
    cursor: pointer !important;
    padding: 2px !important;
    top: 10px !important;
    right: 12px !important;
}

.mycash-driver-popover .driver-popover-close-btn:hover {
    color: #ef4444 !important;
}

/* Highlighted Active Element Ring */
.driver-active-element {
    outline: 2.5px solid #5DCAA5 !important;
    outline-offset: 3px !important;
    border-radius: 12px !important;
    transition: outline 0.25s ease, outline-offset 0.25s ease !important;
}

/* SVG Overlay & Stage Transitions */
.driver-overlay {
    transition: opacity 0.35s ease !important;
}
.driver-overlay path {
    transition: d 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

/* Arrow Driver.js */
.driver-popover-arrow {
    border-color: #ffffff !important;
}
</style>

