<?php

namespace Tests\Feature;

use App\Models\Bendahara;
use App\Models\DetailTransaksiKas;
use App\Models\Kelas;
use App\Models\Pengumuman;
use App\Models\PengumumanPenerima;
use App\Models\Siswa;
use App\Models\TransaksiKas;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPaymentNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_transaction_entry_creates_notification_for_student(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $this->assertNotNull($bendaharaUser);

        $bendahara = $bendaharaUser->bendahara;
        $kelas = $bendahara->kelas;
        $student = Siswa::where('kode_kelas', $kelas->kode_kelas)->first();
        $this->assertNotNull($student);

        // Clear existing notifications for this student
        PengumumanPenerima::where('id_siswa', $student->id)->delete();
        DetailTransaksiKas::where('id_siswa', $student->id)->delete();

        $feeAmount = (float)($kelas->nominal_standar ?? 20000);

        // Bendahara enters transaction for this student
        $response = $this->actingAs($bendaharaUser)->post(route('bendahara.transactions.store'), [
            'type' => 'income',
            'amount' => $feeAmount,
            'description' => 'Iuran Kas Siswa Bulan Januari',
            'transaction_date' => now()->format('Y-m-d'),
            'category' => 'Uang Kas',
            'student_id' => $student->id,
        ]);

        $response->assertRedirect(route('bendahara.transactions.index'));

        // Assert notification was created specifically for this student
        $penerima = PengumumanPenerima::where('id_siswa', $student->id)->latest()->first();
        $this->assertNotNull($penerima);
        $this->assertFalse($penerima->is_read);

        $announcement = $penerima->pengumuman;
        $this->assertNotNull($announcement);

        // Assert nominal and month are in the title or content
        $this->assertStringContainsString('Pembayaran Kas Berhasil', $announcement->judul);
        $this->assertStringContainsString(number_format($feeAmount, 0, ',', '.'), $announcement->judul);

        // Assert content contains nominal, month, and notification date
        $this->assertStringContainsString('Nominal Dibayar', $announcement->isi);
        $this->assertStringContainsString('Untuk Bulan/Periode', $announcement->isi);
        $this->assertStringContainsString('Tanggal Notifikasi', $announcement->isi);
        $this->assertStringContainsString('Tanggal Transaksi', $announcement->isi);

        // Now login as the student and verify notification display
        $studentUser = $student->user;
        $this->assertNotNull($studentUser);

        // 1. Dashboard shows notification
        $dashResponse = $this->actingAs($studentUser)->get(route('siswa.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Pembayaran Kas');
        $dashResponse->assertSee($announcement->judul);

        // 2. Notifications index shows notification
        $indexResponse = $this->actingAs($studentUser)->get(route('siswa.notifications.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee($announcement->judul);
        $indexResponse->assertSee('1 Baru');

        // 3. Notification show marks as read
        $showResponse = $this->actingAs($studentUser)->get(route('siswa.notifications.show', $penerima->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Pembayaran Kas');
        $showResponse->assertSee('Nominal Dibayar');
        $showResponse->assertSee('Untuk Bulan/Periode');
        $showResponse->assertSee('Tanggal Notifikasi');

        // Assert is_read is now true
        $penerima->refresh();
        $this->assertTrue($penerima->is_read);
        $this->assertNotNull($penerima->read_at);
    }

    public function test_matrix_checklist_toggle_creates_notification_for_student(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $bendahara = $bendaharaUser->bendahara;
        $kelas = $bendahara->kelas;
        $student = Siswa::where('kode_kelas', $kelas->kode_kelas)->first();

        // Clear existing notifications
        PengumumanPenerima::where('id_siswa', $student->id)->delete();
        DetailTransaksiKas::where('id_siswa', $student->id)->delete();

        // Toggle kas via matrix
        $response = $this->actingAs($bendaharaUser)->postJson(route('bendahara.students.toggleAbsensi'), [
            'student_id' => $student->id,
            'period' => 'Jan',
            'status' => true,
        ]);

        $response->assertJson(['success' => true]);

        // Assert notification created for student
        $penerima = PengumumanPenerima::where('id_siswa', $student->id)->latest()->first();
        $this->assertNotNull($penerima);
        $this->assertStringContainsString('Pembayaran Kas Berhasil', $penerima->pengumuman->judul);
        $this->assertStringContainsString('Januari', $penerima->pengumuman->isi);
        $this->assertStringContainsString('Tanggal Notifikasi', $penerima->pengumuman->isi);
    }

    public function test_realtime_check_unread_endpoint_returns_new_notification(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $bendahara = $bendaharaUser->bendahara;
        $kelas = $bendahara->kelas;
        $student = Siswa::where('kode_kelas', $kelas->kode_kelas)->first();
        $studentUser = $student->user;

        // Clear existing notifications
        PengumumanPenerima::where('id_siswa', $student->id)->delete();

        // 1. Initial check with last_id = 0
        $response = $this->actingAs($studentUser)->getJson(route('siswa.notifications.checkUnread', ['last_id' => 0]));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'has_new' => false,
            'unread_count' => 0,
        ]);

        $initialLastId = $response->json('last_id');

        // 2. Bendahara enters payment for this student
        $feeAmount = (float)($kelas->nominal_standar ?? 20000);
        $this->actingAs($bendaharaUser)->post(route('bendahara.transactions.store'), [
            'type' => 'income',
            'amount' => $feeAmount,
            'description' => 'Iuran Kas Realtime Test',
            'transaction_date' => now()->format('Y-m-d'),
            'category' => 'Uang Kas',
            'student_id' => $student->id,
        ]);

        // 3. Student polls checkUnread with initialLastId
        $pollResponse = $this->actingAs($studentUser)->getJson(route('siswa.notifications.checkUnread', ['last_id' => $initialLastId]));
        $pollResponse->assertStatus(200);
        $pollResponse->assertJson([
            'success' => true,
            'has_new' => true,
            'unread_count' => 1,
            'notification' => [
                'is_payment' => true,
            ],
        ]);
        $this->assertStringContainsString('Pembayaran Kas Berhasil', $pollResponse->json('notification.judul'));
    }
}
