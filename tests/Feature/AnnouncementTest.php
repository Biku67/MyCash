<?php

namespace Tests\Feature;

use App\Models\Bendahara;
use App\Models\Kelas;
use App\Models\Pengumuman;
use App\Models\PengumumanPenerima;
use App\Models\Siswa;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_bendahara_can_view_announcements_index(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $this->assertNotNull($bendaharaUser);

        $response = $this->actingAs($bendaharaUser)->get(route('bendahara.announcements.index'));
        $response->assertStatus(200);
        $response->assertSee('Pengumuman Kelas');
        $response->assertSee('Buat Pengumuman');
    }

    public function test_bendahara_can_create_announcement_and_distribute_to_students(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $bendahara = $bendaharaUser->bendahara;
        $classStudentsCount = Siswa::where('kode_kelas', $bendahara->kode_kelas)->count();
        $this->assertGreaterThan(0, $classStudentsCount);

        $response = $this->actingAs($bendaharaUser)->post(route('bendahara.announcements.store'), [
            'judul' => 'Pengumuman Kas Ujian Akhir Semester',
            'isi' => 'Dimohon seluruh siswa melunasi kas sebelum minggu depan.',
            'target_type' => 'all',
        ]);

        $response->assertRedirect(route('bendahara.announcements.index'));
        $response->assertSessionHas('success');

        $announcement = Pengumuman::where('judul', 'Pengumuman Kas Ujian Akhir Semester')->first();
        $this->assertNotNull($announcement);
        $this->assertEquals($bendahara->kode_kelas, $announcement->kode_kelas);

        $recipientsCount = PengumumanPenerima::where('id_pengumuman', $announcement->id)->count();
        $this->assertEquals($classStudentsCount, $recipientsCount);

        // Every recipient starts with is_read = false
        $unreadCount = PengumumanPenerima::where('id_pengumuman', $announcement->id)->where('is_read', false)->count();
        $this->assertEquals($classStudentsCount, $unreadCount);
    }

    public function test_bendahara_can_create_announcement_for_specific_students(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $bendahara = $bendaharaUser->bendahara;
        $students = Siswa::where('kode_kelas', $bendahara->kode_kelas)->take(2)->get();
        $this->assertCount(2, $students);

        $response = $this->actingAs($bendaharaUser)->post(route('bendahara.announcements.store'), [
            'judul' => 'Tagihan Khusus Tunggakan Kas',
            'isi' => 'Harap segera lunasi tunggakan kas Anda 2 minggu berturut-turut.',
            'target_type' => 'selected',
            'student_ids' => $students->pluck('id')->toArray(),
        ]);

        $response->assertRedirect(route('bendahara.announcements.index'));
        $response->assertSessionHas('success');

        $announcement = Pengumuman::where('judul', 'Tagihan Khusus Tunggakan Kas')->first();
        $this->assertNotNull($announcement);

        $recipients = PengumumanPenerima::where('id_pengumuman', $announcement->id)->get();
        $this->assertCount(2, $recipients);
        $this->assertEqualsCanonicalizing($students->pluck('id')->toArray(), $recipients->pluck('id_siswa')->toArray());
    }

    public function test_bendahara_can_view_reader_status_breakdown(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $bendahara = $bendaharaUser->bendahara;

        $announcement = Pengumuman::create([
            'kode_kelas' => $bendahara->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'judul' => 'Rapat Kas Kelas',
            'isi' => 'Rapat kas diadakan jam istirahat pertama.',
        ]);

        $students = Siswa::where('kode_kelas', $bendahara->kode_kelas)->get();
        foreach ($students as $student) {
            PengumumanPenerima::create([
                'id_pengumuman' => $announcement->id,
                'id_siswa' => $student->id,
                'is_read' => false,
            ]);
        }

        $response = $this->actingAs($bendaharaUser)->get(route('bendahara.announcements.show', $announcement->id));
        $response->assertStatus(200);
        $response->assertSee('Rapat Kas Kelas');
        $response->assertSee('Daftar Status Siswa');
        $response->assertSee('Belum Membaca');
    }

    public function test_bendahara_can_update_announcement(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $bendahara = $bendaharaUser->bendahara;

        $announcement = Pengumuman::create([
            'kode_kelas' => $bendahara->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'judul' => 'Judul Lama',
            'isi' => 'Isi Lama',
        ]);

        $response = $this->actingAs($bendaharaUser)->put(route('bendahara.announcements.update', $announcement->id), [
            'judul' => 'Judul Baru Diperbarui',
            'isi' => 'Isi baru telah diperbarui oleh bendahara.',
        ]);

        $response->assertRedirect(route('bendahara.announcements.index'));
        $announcement->refresh();
        $this->assertEquals('Judul Baru Diperbarui', $announcement->judul);
    }

    public function test_bendahara_can_delete_announcement(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $bendahara = $bendaharaUser->bendahara;

        $announcement = Pengumuman::create([
            'kode_kelas' => $bendahara->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'judul' => 'Pengumuman Dihapus',
            'isi' => 'Akan segera dihapus.',
        ]);

        $student = Siswa::where('kode_kelas', $bendahara->kode_kelas)->first();
        PengumumanPenerima::create([
            'id_pengumuman' => $announcement->id,
            'id_siswa' => $student->id,
            'is_read' => false,
        ]);

        $response = $this->actingAs($bendaharaUser)->delete(route('bendahara.announcements.destroy', $announcement->id));
        $response->assertRedirect(route('bendahara.announcements.index'));

        $this->assertDatabaseMissing('pengumuman', ['id' => $announcement->id]);
        $this->assertDatabaseMissing('pengumuman_penerima', ['id_pengumuman' => $announcement->id]);
    }

    public function test_siswa_can_view_notifications_list(): void
    {
        $siswaUser = User::where('role', 'siswa')->first();
        $this->assertNotNull($siswaUser);

        $response = $this->actingAs($siswaUser)->get(route('siswa.notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('Notifikasi & Pengumuman');
    }

    public function test_siswa_can_open_announcement_and_automatically_mark_as_read(): void
    {
        $siswaUser = User::where('role', 'siswa')->first();
        $siswa = $siswaUser->siswa;
        $bendahara = Bendahara::where('kode_kelas', $siswa->kode_kelas)->first();

        $announcement = Pengumuman::create([
            'kode_kelas' => $siswa->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'judul' => 'Pengumuman Khusus Siswa',
            'isi' => 'Isi penting pengumuman untuk siswa.',
        ]);

        $recipient = PengumumanPenerima::create([
            'id_pengumuman' => $announcement->id,
            'id_siswa' => $siswa->id,
            'is_read' => false,
            'read_at' => null,
        ]);

        $this->assertFalse($recipient->is_read);

        $response = $this->actingAs($siswaUser)->get(route('siswa.notifications.show', $recipient->id));
        $response->assertStatus(200);
        $response->assertSee('Pengumuman Khusus Siswa');
        $response->assertSee('Telah Dibaca');

        $recipient->refresh();
        $this->assertTrue($recipient->is_read);
        $this->assertNotNull($recipient->read_at);
    }

    public function test_siswa_can_mark_all_announcements_as_read(): void
    {
        $siswaUser = User::where('role', 'siswa')->first();
        $siswa = $siswaUser->siswa;
        $bendahara = Bendahara::where('kode_kelas', $siswa->kode_kelas)->first();

        $ann1 = Pengumuman::create([
            'kode_kelas' => $siswa->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'judul' => 'Pengumuman 1',
            'isi' => 'Isi 1',
        ]);
        $ann2 = Pengumuman::create([
            'kode_kelas' => $siswa->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'judul' => 'Pengumuman 2',
            'isi' => 'Isi 2',
        ]);

        $rec1 = PengumumanPenerima::create([
            'id_pengumuman' => $ann1->id,
            'id_siswa' => $siswa->id,
            'is_read' => false,
        ]);
        $rec2 = PengumumanPenerima::create([
            'id_pengumuman' => $ann2->id,
            'id_siswa' => $siswa->id,
            'is_read' => false,
        ]);

        $response = $this->actingAs($siswaUser)->post(route('siswa.notifications.markAllAsRead'));
        $response->assertStatus(302);

        $rec1->refresh();
        $rec2->refresh();

        $this->assertTrue($rec1->is_read);
        $this->assertTrue($rec2->is_read);
    }

    public function test_non_bendahara_cannot_access_bendahara_announcements(): void
    {
        $siswaUser = User::where('role', 'siswa')->first();

        $response = $this->actingAs($siswaUser)->get(route('bendahara.announcements.index'));
        $response->assertStatus(403);
    }

    public function test_bendahara_can_filter_announcements_and_payment_notifications(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $bendahara = $bendaharaUser->bendahara;
        $student = Siswa::where('kode_kelas', $bendahara->kode_kelas)->first();

        // Create 1 manual announcement and 1 payment notification
        $manual = Pengumuman::create([
            'kode_kelas' => $bendahara->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'judul' => 'Pengumuman Kerja Bakti Kelas',
            'isi' => 'Besok ada kerja bakti kelas.',
        ]);

        $payment = Pengumuman::create([
            'kode_kelas' => $bendahara->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'judul' => 'Pembayaran Kas Berhasil: Rp 20.000 (Januari)',
            'isi' => 'Rincian Pembayaran Kas untuk ' . $student->nama,
        ]);
        PengumumanPenerima::create([
            'id_pengumuman' => $payment->id,
            'id_siswa' => $student->id,
            'is_read' => false,
        ]);

        // 1. Filter type=pengumuman
        $responseManual = $this->actingAs($bendaharaUser)->get(route('bendahara.announcements.index', ['type' => 'pengumuman']));
        $responseManual->assertStatus(200);
        $responseManual->assertSee('Pengumuman Kerja Bakti Kelas');
        $responseManual->assertDontSee('Pembayaran Kas Berhasil');

        // 2. Filter type=pembayaran
        $responsePayment = $this->actingAs($bendaharaUser)->get(route('bendahara.announcements.index', ['type' => 'pembayaran']));
        $responsePayment->assertStatus(200);
        $responsePayment->assertSee('Pembayaran Kas Berhasil');
        $responsePayment->assertDontSee('Pengumuman Kerja Bakti Kelas');
        $responsePayment->assertSee('Notifikasi Kas');

        // 3. Search filter
        $responseSearch = $this->actingAs($bendaharaUser)->get(route('bendahara.announcements.index', ['search' => 'Bakti']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Pengumuman Kerja Bakti Kelas');
        $responseSearch->assertDontSee('Pembayaran Kas Berhasil');

        // 4. Edit protection for payment notification
        $responseEdit = $this->actingAs($bendaharaUser)->get(route('bendahara.announcements.edit', $payment->id));
        $responseEdit->assertRedirect(route('bendahara.announcements.index'));
        $responseEdit->assertSessionHas('error');
    }
}
