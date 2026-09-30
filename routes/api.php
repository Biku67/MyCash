<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\TransaksiKas;
use App\Models\DetailTransaksiKas;
use App\Models\Kategori;
use App\Models\Siswa;
use App\Models\Bendahara;
use App\Models\Kelas;

// 1. Endpoint untuk mengecek data master (Siswa, Bendahara, Kategori, Kelas)
Route::get('/test/master-data', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Data master untuk parameter pengujian Postman',
        'data' => [
            'bendahara' => Bendahara::with('kelas')->get(),
            'siswa' => Siswa::all(),
            'kategori' => Kategori::all(),
            'kelas' => Kelas::all(),
        ]
    ]);
});

// 2. Endpoint untuk melihat seluruh transaksi beserta rinciannya
Route::get('/test/transaksi', function () {
    $transaksi = TransaksiKas::with(['kategori', 'bendahara', 'rincian.siswa'])->latest()->get();
    return response()->json([
        'status' => 'success',
        'total' => $transaksi->count(),
        'data' => $transaksi
    ]);
});

// 3. Endpoint untuk mencatat transaksi baru (Simulasi Postman)
Route::post('/test/transaksi', function (Request $request) {
    $validated = $request->validate([
        'type' => 'required|in:income,expense',
        'amount' => 'required|numeric|min:0',
        'description' => 'required|string',
        'transaction_date' => 'required|date',
        'category' => 'required|string',
        'student_id' => 'required_if:category,Uang Kas|nullable|exists:siswa,id',
        'bendahara_id' => 'nullable|exists:bendahara,id',
    ]);

    return DB::transaction(function () use ($validated, $request) {
        // Ambil bendahara (default ke bendahara pertama jika tidak dikirim)
        $bendahara = isset($validated['bendahara_id']) 
            ? Bendahara::with('kelas')->find($validated['bendahara_id']) 
            : Bendahara::with('kelas')->first();

        if (!$bendahara) {
            return response()->json(['status' => 'error', 'message' => 'Data Bendahara tidak ditemukan. Jalankan seeder terlebih dahulu.'], 404);
        }

        $kelas = $bendahara->kelas;
        $studentId = $validated['student_id'] ?? null;
        $description = $validated['description'];

        $kategori = Kategori::where('nama_kategori', $validated['category'])->first();
        if (!$kategori) {
            $kategori = Kategori::create([
                'nama_kategori' => $validated['category'],
                'tipe' => $validated['type'] === 'income' ? 'pemasukan' : 'pengeluaran'
            ]);
        }

        // Simpan TransaksiKas (Header / Induk Nota)
        $transaction = TransaksiKas::create([
            'kode_kelas' => $bendahara->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'id_kategori' => $kategori->id,
            'jenis_transaksi' => $validated['type'] === 'income' ? 'pemasukan' : 'pengeluaran',
            'total_nominal' => $validated['amount'],
            'tanggal_transaksi' => $validated['transaction_date'],
            'keterangan' => $description,
        ]);

        $allocatedDetails = [];

        // Logika Alokasi jika Kategori Uang Kas
        if ($validated['type'] === 'income' && $validated['category'] === 'Uang Kas' && $studentId) {
            $student = Siswa::where('id', $studentId)
                ->where('kode_kelas', $bendahara->kode_kelas)
                ->firstOrFail();

            $nominalStandar = $kelas->nominal_standar ?? 20000;
            $jumlahPeriode = floor($validated['amount'] / $nominalStandar);

            $periods = $kelas ? $kelas->getPeriods() : [];

            $checkedPeriods = DetailTransaksiKas::where('id_siswa', $student->id)
                ->pluck('periode')
                ->toArray();

            $periodsToCheck = [];
            foreach ($periods as $period) {
                if (!in_array($period, $checkedPeriods)) {
                    $periodsToCheck[] = $period;
                    if (count($periodsToCheck) == $jumlahPeriode) {
                        break;
                    }
                }
            }

            // Simpan detail untuk tiap periode penuh
            foreach ($periodsToCheck as $period) {
                $detail = DetailTransaksiKas::create([
                    'id_transaksi_kas' => $transaction->id,
                    'id_siswa' => $student->id,
                    'periode' => $period,
                    'nominal' => $nominalStandar,
                ]);
                $allocatedDetails[] = $detail;
            }

            // Simpan sisa parsial jika ada
            $allocatedAmount = count($periodsToCheck) * $nominalStandar;
            $remainingAmount = $validated['amount'] - $allocatedAmount;

            if ($remainingAmount > 0) {
                $nextPeriod = null;
                foreach ($periods as $period) {
                    if (!in_array($period, $checkedPeriods) && !in_array($period, $periodsToCheck)) {
                        $nextPeriod = $period;
                        break;
                    }
                }

                $detail = DetailTransaksiKas::create([
                    'id_transaksi_kas' => $transaction->id,
                    'id_siswa' => $student->id,
                    'periode' => $nextPeriod,
                    'nominal' => $remainingAmount,
                ]);
                $allocatedDetails[] = $detail;

                if ($nextPeriod) {
                    $periodsToCheck[] = $nextPeriod . ' (Partial)';
                }
            }

            if (count($periodsToCheck) > 0) {
                $transaction->update([
                    'keterangan' => $description . ' (' . implode(', ', $periodsToCheck) . ')'
                ]);
            }
        } elseif ($validated['type'] === 'income') {
            $detail = DetailTransaksiKas::create([
                'id_transaksi_kas' => $transaction->id,
                'id_siswa' => null,
                'periode' => null,
                'nominal' => $validated['amount'],
            ]);
            $allocatedDetails[] = $detail;
        } else {
            $detail = DetailTransaksiKas::create([
                'id_transaksi_kas' => $transaction->id,
                'id_siswa' => null,
                'periode' => null,
                'nominal' => -$validated['amount'],
            ]);
            $allocatedDetails[] = $detail;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi berhasil dicatat dan dialokasikan ke rincian kas.',
            'data' => [
                'transaksi_header' => $transaction,
                'rincian_alokasi' => $allocatedDetails
            ]
        ], 201);
    });
});

// ─── 4. MOBILE APP ENDPOINTS (React Native / Mobile Client) ───────────────────────

// API Mobile: Login (Mendukung Email atau NIS)
Route::post('/mobile/login', function (Request $request) {
    $request->validate([
        'login' => 'required|string',
        'password' => 'required|string',
    ]);

    $login = $request->input('login');
    $password = $request->input('password');

    // Cari user via email atau NIS (Siswa / Bendahara)
    $user = null;
    if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
        $user = \App\Models\User::where('email', $login)->first();
    } else {
        $siswa = \App\Models\Siswa::where('nis', $login)->first();
        if ($siswa && $siswa->user) {
            $user = $siswa->user;
        } else {
            $bendahara = \App\Models\Bendahara::where('nis', $login)->first();
            if ($bendahara && $bendahara->user) {
                $user = $bendahara->user;
            }
        }
    }

    if (!$user || !\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
        return response()->json([
            'status' => 'error',
            'message' => 'NIS/Email atau Password salah.'
        ], 401);
    }

    if (!$user->is_active) {
        return response()->json([
            'status' => 'error',
            'message' => 'Akun Anda tidak aktif. Silakan hubungi administrator.'
        ], 403);
    }

    $role = $user->roles->first()?->name ?? $user->role ?? 'siswa';

    $siswaData = null;
    $bendaharaData = null;
    $waliKelasData = null;
    $kodeKelas = null;

    if ($user->siswa) {
        $siswaData = [
            'id' => $user->siswa->id,
            'nama' => $user->siswa->nama,
            'nis' => $user->siswa->nis,
            'kode_kelas' => $user->siswa->kode_kelas,
        ];
        $kodeKelas = $user->siswa->kode_kelas;
    } elseif ($user->bendahara) {
        $bendaharaData = [
            'id' => $user->bendahara->id,
            'nama' => $user->bendahara->nama,
            'kode_kelas' => $user->bendahara->kode_kelas,
        ];
        $kodeKelas = $user->bendahara->kode_kelas;
    } elseif ($user->waliKelas) {
        $waliKelasData = [
            'id' => $user->waliKelas->id,
            'nama' => $user->waliKelas->nama,
        ];
        $kodeKelas = $user->waliKelas->kelas->first()?->kode_kelas;
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Login berhasil!',
        'data' => [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $role,
            'kode_kelas' => $kodeKelas,
            'siswa' => $siswaData,
            'bendahara' => $bendaharaData,
            'wali_kelas' => $waliKelasData,
        ]
    ]);
});

// API Mobile: Dashboard (Statistik Kas Kelas & Status Kelunasan Siswa)
Route::get('/mobile/dashboard', function (Request $request) {
    $userId = $request->query('user_id');
    $kodeKelas = $request->query('kode_kelas');

    $user = \App\Models\User::find($userId);
    $student = $user?->siswa;

    if (!$kodeKelas && $student) {
        $kodeKelas = $student->kode_kelas;
    }

    $kelas = \App\Models\Kelas::where('kode_kelas', $kodeKelas)->first();

    if (!$kelas) {
        $kelas = \App\Models\Kelas::first();
        $kodeKelas = $kelas?->kode_kelas;
    }

    $feePeriodType = $kelas->tipe_periode ?? 'bulanan';
    $feeAmount = (float)($kelas->nominal_standar ?? 20000);
    $periods = $kelas ? $kelas->getPeriods() : [];

    // Ringkasan Pembayaran Siswa (jika login sebagai siswa)
    $studentStatus = null;
    if ($student) {
        $details = \App\Models\DetailTransaksiKas::where('id_siswa', $student->id)
            ->whereNotNull('periode')
            ->get();

        $periodTotals = $details->groupBy('periode')->map(fn($group) => (float)$group->sum('nominal'));
        $paidPeriods = [];
        $partialPeriods = [];
        foreach ($periods as $period) {
            $periodPaid = (float)$periodTotals->get($period, 0.0);
            if ($periodPaid >= $feeAmount) {
                $paidPeriods[] = $period;
            } elseif ($periodPaid > 0) {
                $partialPeriods[$period] = $periodPaid;
            }
        }

        $contributed = (float)$details->sum('nominal');
        $totalTarget = count($periods) * $feeAmount;
        $outstandingDebt = max(0.0, $totalTarget - $contributed);

        $statusStr = 'Up to Date';
        if ($outstandingDebt > 0) {
            $statusStr = $contributed > 0 ? 'Pending' : 'Overdue';
        }

        $studentStatus = [
            'id_siswa' => $student->id,
            'nama' => $student->nama,
            'nis' => $student->nis,
            'contributed' => $contributed,
            'outstanding_debt' => $outstandingDebt,
            'status' => $statusStr,
            'paid_periods' => $paidPeriods,
            'partial_periods' => $partialPeriods,
        ];
    }

    // Rekap Keseluruhan Kas Kelas
    $pemasukan = (float)\App\Models\TransaksiKas::where('kode_kelas', $kodeKelas)
        ->where('jenis_transaksi', 'pemasukan')
        ->sum('total_nominal');

    $pengeluaran = (float)\App\Models\TransaksiKas::where('kode_kelas', $kodeKelas)
        ->where('jenis_transaksi', 'pengeluaran')
        ->sum('total_nominal');

    return response()->json([
        'status' => 'success',
        'data' => [
            'kelas' => [
                'kode_kelas' => $kelas?->kode_kelas,
                'nama_kelas' => $kelas?->nama_kelas,
                'saldo_kas' => (float)($kelas?->saldo_kas ?? ($pemasukan - $pengeluaran)),
                'tipe_periode' => $feePeriodType,
                'nominal_standar' => $feeAmount,
                'total_pemasukan' => $pemasukan,
                'total_pengeluaran' => $pengeluaran,
            ],
            'periods' => $periods,
            'siswa_status' => $studentStatus,
        ]
    ]);
});

// API Mobile: Riwayat Transaksi Kas
Route::get('/mobile/history', function (Request $request) {
    $kodeKelas = $request->query('kode_kelas');

    if (!$kodeKelas) {
        $userId = $request->query('user_id');
        $user = \App\Models\User::find($userId);
        $kodeKelas = $user?->siswa?->kode_kelas ?? $user?->bendahara?->kode_kelas ?? \App\Models\Kelas::first()?->kode_kelas;
    }

    $transactions = \App\Models\TransaksiKas::with('kategori')
        ->where('kode_kelas', $kodeKelas)
        ->latest('tanggal_transaksi')
        ->latest('id')
        ->take(30)
        ->get()
        ->map(function ($t) {
            return [
                'id' => $t->id,
                'jenis_transaksi' => $t->jenis_transaksi,
                'total_nominal' => (float)$t->total_nominal,
                'tanggal_transaksi' => $t->tanggal_transaksi?->format('d M Y') ?? (string)$t->tanggal_transaksi,
                'kategori' => $t->kategori?->nama_kategori ?? 'Umum',
                'keterangan' => $t->keterangan ?? '-',
            ];
        });

    return response()->json([
        'status' => 'success',
        'total' => $transactions->count(),
        'data' => $transactions
    ]);
});
