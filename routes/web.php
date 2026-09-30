<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('bendahara')) {
        return redirect()->route('bendahara.dashboard');
    } elseif ($user->hasRole('wali_kelas')) {
        return redirect()->route('wali-kelas.dashboard');
    } elseif ($user->hasRole('siswa')) {
        return redirect()->route('siswa.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout.get');
});

Route::middleware(['auth', 'role:bendahara'])->group(function () {
    // Dashboard Bendahara
    Route::get('/bendahara/dashboard', [App\Http\Controllers\Bendahara\DashboardController::class, 'index'])->name('bendahara.dashboard');

    // ─── Checklist Kas & Presensi Siswa (Bendahara)
    Route::get('/bendahara/students', [App\Http\Controllers\Bendahara\StudentController::class, 'index'])->name('bendahara.students.index');
    Route::post('/bendahara/students/fee-settings', [App\Http\Controllers\Bendahara\StudentController::class, 'updateFeeSettings'])->name('bendahara.students.updateFeeSettings');
    Route::post('/bendahara/students/tutup-buku', [App\Http\Controllers\Bendahara\StudentController::class, 'tutupBuku'])->name('bendahara.students.tutupBuku');
    Route::post('/bendahara/students/reset-matrix', [App\Http\Controllers\Bendahara\StudentController::class, 'resetMatrix'])->name('bendahara.students.resetMatrix');
    Route::post('/bendahara/students/toggle-absensi', [App\Http\Controllers\Bendahara\StudentController::class, 'toggleAbsensi'])->name('bendahara.students.toggleAbsensi');
    Route::get('/bendahara/students/export', [App\Http\Controllers\Bendahara\StudentController::class, 'exportExcel'])->name('bendahara.students.export');
    Route::get('/bendahara/students/export-excel', [App\Http\Controllers\Bendahara\StudentController::class, 'exportExcel'])->name('bendahara.students.exportExcel');
    Route::get('/bendahara/students/export-pdf', [App\Http\Controllers\Bendahara\StudentController::class, 'exportPdf'])->name('bendahara.students.exportPdf');

    // ─── Transactions Management
    Route::get('/bendahara/transactions/export', [App\Http\Controllers\Bendahara\TransactionController::class, 'exportExcel'])->name('bendahara.transactions.export');
    Route::get('/bendahara/transactions/export-pdf', [App\Http\Controllers\Bendahara\TransactionController::class, 'exportPdf'])->name('bendahara.transactions.exportPdf');
    Route::get('/bendahara/transactions/logs', [App\Http\Controllers\Bendahara\TransactionController::class, 'logs'])->name('bendahara.transactions.logs');
    Route::get('/bendahara/transactions', [App\Http\Controllers\Bendahara\TransactionController::class, 'index'])->name('bendahara.transactions.index');
    Route::get('/bendahara/transactions/create', [App\Http\Controllers\Bendahara\TransactionController::class, 'create'])->name('bendahara.transactions.create');
    Route::post('/bendahara/transactions', [App\Http\Controllers\Bendahara\TransactionController::class, 'store'])->name('bendahara.transactions.store');
    Route::get('/bendahara/transactions/{id}/edit', [App\Http\Controllers\Bendahara\TransactionController::class, 'edit'])->name('bendahara.transactions.edit');
    Route::put('/bendahara/transactions/{id}', [App\Http\Controllers\Bendahara\TransactionController::class, 'update'])->name('bendahara.transactions.update');
    Route::delete('/bendahara/transactions/{id}', [App\Http\Controllers\Bendahara\TransactionController::class, 'destroy'])->name('bendahara.transactions.destroy');

    // ─── Financial Reports (Bendahara)
    Route::get('/bendahara/report', [App\Http\Controllers\Bendahara\ReportController::class, 'index'])->name('bendahara.report.index');
    Route::get('/bendahara/report/export-excel', [App\Http\Controllers\Bendahara\ReportController::class, 'exportExcel'])->name('bendahara.report.exportExcel');
    Route::get('/bendahara/report/export-pdf', [App\Http\Controllers\Bendahara\ReportController::class, 'exportPdf'])->name('bendahara.report.exportPdf');

    // ─── Announcements (Bendahara)
    Route::get('/bendahara/announcements', [App\Http\Controllers\Bendahara\AnnouncementController::class, 'index'])->name('bendahara.announcements.index');
    Route::get('/bendahara/announcements/create', [App\Http\Controllers\Bendahara\AnnouncementController::class, 'create'])->name('bendahara.announcements.create');
    Route::post('/bendahara/announcements', [App\Http\Controllers\Bendahara\AnnouncementController::class, 'store'])->name('bendahara.announcements.store');
    Route::get('/bendahara/announcements/{id}', [App\Http\Controllers\Bendahara\AnnouncementController::class, 'show'])->name('bendahara.announcements.show');
    Route::get('/bendahara/announcements/{id}/edit', [App\Http\Controllers\Bendahara\AnnouncementController::class, 'edit'])->name('bendahara.announcements.edit');
    Route::put('/bendahara/announcements/{id}', [App\Http\Controllers\Bendahara\AnnouncementController::class, 'update'])->name('bendahara.announcements.update');
    Route::delete('/bendahara/announcements/{id}', [App\Http\Controllers\Bendahara\AnnouncementController::class, 'destroy'])->name('bendahara.announcements.destroy');
});

// ─── Super Admin (Kelola Bendahara, Kelas, Wali Kelas)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Kelola Bendahara
    Route::get('/bendahara/export', [App\Http\Controllers\Admin\BendaharaController::class, 'exportExcel'])->name('bendahara.export');
    Route::get('/bendahara/export-pdf', [App\Http\Controllers\Admin\BendaharaController::class, 'exportPdf'])->name('bendahara.exportPdf');
    Route::get('/bendahara', [App\Http\Controllers\Admin\BendaharaController::class, 'index'])->name('bendahara.index');
    Route::get('/bendahara/create', [App\Http\Controllers\Admin\BendaharaController::class, 'create'])->name('bendahara.create');
    Route::post('/bendahara', [App\Http\Controllers\Admin\BendaharaController::class, 'store'])->name('bendahara.store');
    Route::get('/bendahara/{id}/edit', [App\Http\Controllers\Admin\BendaharaController::class, 'edit'])->name('bendahara.edit');
    Route::put('/bendahara/{id}', [App\Http\Controllers\Admin\BendaharaController::class, 'update'])->name('bendahara.update');
    Route::delete('/bendahara/{id}', [App\Http\Controllers\Admin\BendaharaController::class, 'destroy'])->name('bendahara.destroy');

    // Kelola Kelas
    Route::get('/kelas', [App\Http\Controllers\Admin\KelasController::class, 'index'])->name('kelas.index');
    Route::get('/kelas/create', [App\Http\Controllers\Admin\KelasController::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [App\Http\Controllers\Admin\KelasController::class, 'store'])->name('kelas.store');
    Route::get('/kelas/{id}/edit', [App\Http\Controllers\Admin\KelasController::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{id}', [App\Http\Controllers\Admin\KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{id}', [App\Http\Controllers\Admin\KelasController::class, 'destroy'])->name('kelas.destroy');

    // Kelola Wali Kelas
    Route::get('/wali-kelas', [App\Http\Controllers\Admin\WaliKelasController::class, 'index'])->name('wali-kelas.index');
    Route::get('/wali-kelas/create', [App\Http\Controllers\Admin\WaliKelasController::class, 'create'])->name('wali-kelas.create');
    Route::post('/wali-kelas', [App\Http\Controllers\Admin\WaliKelasController::class, 'store'])->name('wali-kelas.store');
    Route::get('/wali-kelas/{id}/edit', [App\Http\Controllers\Admin\WaliKelasController::class, 'edit'])->name('wali-kelas.edit');
    Route::put('/wali-kelas/{id}', [App\Http\Controllers\Admin\WaliKelasController::class, 'update'])->name('wali-kelas.update');
    Route::delete('/wali-kelas/{id}', [App\Http\Controllers\Admin\WaliKelasController::class, 'destroy'])->name('wali-kelas.destroy');
});

// ─── Wali Kelas (Dashboard & Kelola Siswa Binaan)
Route::middleware(['auth', 'role:wali_kelas'])->prefix('wali-kelas')->name('wali-kelas.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\WaliKelas\DashboardController::class, 'index'])->name('dashboard');

    // Data & Pendaftaran Siswa Binaan
    Route::get('/students', [App\Http\Controllers\WaliKelas\StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [App\Http\Controllers\WaliKelas\StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [App\Http\Controllers\WaliKelas\StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{id}/edit', [App\Http\Controllers\WaliKelas\StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{id}', [App\Http\Controllers\WaliKelas\StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{id}', [App\Http\Controllers\WaliKelas\StudentController::class, 'destroy'])->name('students.destroy');
    Route::post('/students/{id}/reset-password', [App\Http\Controllers\WaliKelas\StudentController::class, 'resetPassword'])->name('students.resetPassword');

    // Laporan Keuangan Kas (Wali Kelas)
    Route::get('/report', [App\Http\Controllers\WaliKelas\ReportController::class, 'index'])->name('report.index');
    Route::get('/report/export-excel', [App\Http\Controllers\WaliKelas\ReportController::class, 'exportExcel'])->name('report.exportExcel');
    Route::get('/report/export-pdf', [App\Http\Controllers\WaliKelas\ReportController::class, 'exportPdf'])->name('report.exportPdf');
    Route::get('/report/export-tunggakan-excel', [App\Http\Controllers\WaliKelas\ReportController::class, 'exportTunggakanExcel'])->name('report.exportTunggakanExcel');
    Route::get('/report/export-tunggakan-pdf', [App\Http\Controllers\WaliKelas\ReportController::class, 'exportTunggakanPdf'])->name('report.exportTunggakanPdf');
});

// ─── Siswa (Dashboard & Riwayat Pembayaran Checklist Kas)
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Siswa\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/history', [App\Http\Controllers\Siswa\HistoryController::class, 'index'])->name('history.index');
    Route::get('/checklist', [App\Http\Controllers\Siswa\HistoryController::class, 'index'])->name('checklist');
    Route::get('/transactions', function () { return 'Laporan Kas'; })->name('transactions.index');
    Route::get('/notifications', [App\Http\Controllers\Siswa\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/check-unread', [App\Http\Controllers\Siswa\NotificationController::class, 'checkUnread'])->name('notifications.checkUnread');
    Route::get('/notifications/{id}', [App\Http\Controllers\Siswa\NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\Siswa\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});

require __DIR__.'/auth.php';
