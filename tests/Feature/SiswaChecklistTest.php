<?php

namespace Tests\Feature;

use App\Models\Bendahara;
use App\Models\DetailTransaksiKas;
use App\Models\Kategori;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TransaksiKas;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaChecklistTest extends TestCase
{
    use RefreshDatabase;

    protected $studentUser;
    protected $otherStudentUser;
    protected $student;
    protected $otherStudent;
    protected $kelas;
    protected $bendahara;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->studentUser = User::where('email', 'budi@siswa.app')->first();
        $this->student = $this->studentUser->siswa;
        $this->kelas = $this->student->kelas;
        $this->bendahara = Bendahara::where('kode_kelas', $this->kelas->kode_kelas)->first();

        $this->otherStudentUser = User::where('email', 'siti@siswa.app')->first();
        $this->otherStudent = $this->otherStudentUser->siswa;
    }

    public function test_student_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('siswa.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang!');
        $response->assertSee($this->student->nama);
        $response->assertSee($this->kelas->nama_kelas);
        $response->assertSee($this->student->nis);
        $response->assertSee(route('siswa.history.index'));
    }

    public function test_student_can_view_history_checklist_page(): void
    {
        $response = $this->actingAs($this->studentUser)->get(route('siswa.history.index'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Pembayaran Kas');
        $response->assertSee($this->student->nama);
        $response->assertSee($this->kelas->nama_kelas);
        $response->assertSee($this->student->nis);
        $response->assertSee('Jan');
        $response->assertSee('Des');
        $response->assertSee('Total Dibayar');
        $response->assertSee('Belum Dibayar');
    }

    public function test_student_history_reflects_only_their_own_payments(): void
    {
        $kategori = Kategori::first();

        // Buat pembayaran Uang Kas untuk Budi (Januari = Lunas, Februari = Dicicil Rp 10.000)
        $tx1 = TransaksiKas::create([
            'kode_kelas' => $this->kelas->kode_kelas,
            'id_bendahara' => $this->bendahara->id,
            'id_kategori' => $kategori->id,
            'jenis_transaksi' => 'pemasukan',
            'total_nominal' => 20000,
            'tanggal_transaksi' => '2026-01-10',
            'keterangan' => 'Kas Budi Januari',
        ]);
        DetailTransaksiKas::create([
            'id_transaksi_kas' => $tx1->id,
            'id_siswa' => $this->student->id,
            'periode' => 'Jan',
            'nominal' => 20000,
        ]);

        $tx2 = TransaksiKas::create([
            'kode_kelas' => $this->kelas->kode_kelas,
            'id_bendahara' => $this->bendahara->id,
            'id_kategori' => $kategori->id,
            'jenis_transaksi' => 'pemasukan',
            'total_nominal' => 10000,
            'tanggal_transaksi' => '2026-02-15',
            'keterangan' => 'Kas Budi Februari Dicicil',
        ]);
        DetailTransaksiKas::create([
            'id_transaksi_kas' => $tx2->id,
            'id_siswa' => $this->student->id,
            'periode' => 'Feb',
            'nominal' => 10000,
        ]);

        // Request Riwayat Pembayaran Budi
        $responseBudi = $this->actingAs($this->studentUser)->get(route('siswa.history.index'));
        $responseBudi->assertStatus(200);
        $responseBudi->assertSee('Total Dibayar');
        $responseBudi->assertSee('Belum Dibayar');
        $responseBudi->assertSee('Rp 30.000'); // 20k + 10k
        $responseBudi->assertSee('Rp 210.000'); // Sisa tunggakan
        $responseBudi->assertSee('partial-badge');
        $responseBudi->assertSee('bg-emerald-50/50');

        // Request Riwayat Pembayaran Siti (belum bayar)
        $responseSiti = $this->actingAs($this->otherStudentUser)->get(route('siswa.history.index'));
        $responseSiti->assertStatus(200);
        $responseSiti->assertSee('Rp 0'); // Belum bayar
        $responseSiti->assertDontSee('Rp 30.000');
    }

    public function test_guest_cannot_view_student_history(): void
    {
        $response = $this->get(route('siswa.history.index'));
        $response->assertRedirect('/login');
    }
}
