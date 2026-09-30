<?php

namespace Tests\Feature;

use App\Models\Bendahara;
use App\Models\DetailTransaksiKas;
use App\Models\Kategori;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TransaksiKas;
use App\Models\User;
use App\Models\WaliKelas;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDeletionIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $waliKelasUser;
    protected $bendaharaUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@mycash.app')->first();
        $this->waliKelasUser = User::where('role', 'wali_kelas')->first();
        $this->bendaharaUser = User::where('role', 'bendahara')->first();
    }

    public function test_student_with_transactions_cannot_be_deleted_by_wali_kelas(): void
    {
        $waliKelas = $this->waliKelasUser->waliKelas;
        $kelas = $waliKelas->kelas()->first();
        $student = Siswa::where('kode_kelas', $kelas->kode_kelas)->first();
        $bendahara = Bendahara::where('kode_kelas', $kelas->kode_kelas)->first();
        $kategori = Kategori::first();

        // Buat transaksi kas yang melibatkan siswa ini
        $tx = TransaksiKas::create([
            'kode_kelas' => $kelas->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'id_kategori' => $kategori->id,
            'jenis_transaksi' => 'pemasukan',
            'total_nominal' => 20000,
            'tanggal_transaksi' => now()->toDateString(),
            'keterangan' => 'Uang Kas Pembayaran Uji Coba',
        ]);

        DetailTransaksiKas::create([
            'id_transaksi_kas' => $tx->id,
            'id_siswa' => $student->id,
            'periode' => 'Januari',
            'nominal' => 20000,
        ]);

        $response = $this->actingAs($this->waliKelasUser)
            ->delete(route('wali-kelas.students.destroy', $student->id));

        $response->assertRedirect(route('wali-kelas.students.index'));
        $response->assertSessionHas('error');

        // Pastikan data siswa & usernya tetap ada di database
        $this->assertDatabaseHas('siswa', ['id' => $student->id]);
        $this->assertDatabaseHas('users', ['id' => $student->user_id]);
    }

    public function test_student_without_transactions_can_be_deleted_by_wali_kelas(): void
    {
        $waliKelas = $this->waliKelasUser->waliKelas;
        $kelas = $waliKelas->kelas()->first();

        // Buat siswa baru tanpa transaksi
        $user = User::create([
            'name' => 'Siswa Baru Tanpa Transaksi',
            'email' => 'siswa.tanpa.tx@example.com',
            'password' => bcrypt('password123'),
            'role' => 'siswa',
            'is_active' => true,
        ]);
        $user->assignRole('siswa');

        $student = Siswa::create([
            'user_id' => $user->id,
            'kode_kelas' => $kelas->kode_kelas,
            'nama' => 'Siswa Baru Tanpa Transaksi',
            'nis' => '12345999',
        ]);

        $this->assertEquals(0, DetailTransaksiKas::where('id_siswa', $student->id)->count());

        $response = $this->actingAs($this->waliKelasUser)
            ->delete(route('wali-kelas.students.destroy', $student->id));

        $response->assertRedirect(route('wali-kelas.students.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('siswa', ['id' => $student->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_bendahara_with_recorded_transactions_cannot_be_deleted_by_admin(): void
    {
        $bendahara = Bendahara::first();
        $kategori = Kategori::first();

        // Buat transaksi yang dicatat oleh bendahara ini
        TransaksiKas::create([
            'kode_kelas' => $bendahara->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'id_kategori' => $kategori->id,
            'jenis_transaksi' => 'pengeluaran',
            'total_nominal' => 15000,
            'tanggal_transaksi' => now()->toDateString(),
            'keterangan' => 'Beli Spidol Kelas',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.bendahara.destroy', $bendahara->id));

        $response->assertRedirect(route('admin.bendahara.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('bendahara', ['id' => $bendahara->id]);
    }

    public function test_bendahara_without_transactions_can_be_deleted_by_admin(): void
    {
        $kelas = Kelas::first();

        $user = User::create([
            'name' => 'Bendahara Baru Tanpa Tx',
            'email' => 'bendahara.baru@example.com',
            'password' => bcrypt('password123'),
            'role' => 'bendahara',
            'is_active' => true,
        ]);
        $user->assignRole('bendahara');

        $bendahara = Bendahara::create([
            'user_id' => $user->id,
            'kode_kelas' => $kelas->kode_kelas,
            'nama' => 'Bendahara Baru Tanpa Tx',
            'nis' => '99881122',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.bendahara.destroy', $bendahara->id));

        $response->assertRedirect(route('admin.bendahara.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('bendahara', ['id' => $bendahara->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_kelas_with_students_or_transactions_cannot_be_deleted_by_admin(): void
    {
        $kelasWithRelations = Kelas::whereHas('siswa')->orWhereHas('transaksiKas')->first();
        $this->assertNotNull($kelasWithRelations);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.kelas.destroy', $kelasWithRelations->id));

        $response->assertRedirect(route('admin.kelas.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('kelas', ['id' => $kelasWithRelations->id]);
    }

    public function test_kelas_without_relations_can_be_deleted_by_admin(): void
    {
        $emptyKelas = Kelas::create([
            'kode_kelas' => 'KLS-EMPTY-01',
            'nama_kelas' => 'Kelas Kosong Baru',
            'nominal_standar' => 25000,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.kelas.destroy', $emptyKelas->id));

        $response->assertRedirect(route('admin.kelas.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('kelas', ['id' => $emptyKelas->id]);
    }

    public function test_wali_kelas_assigned_to_class_cannot_be_deleted_by_admin(): void
    {
        $assignedWali = WaliKelas::whereHas('kelas')->first();
        $this->assertNotNull($assignedWali);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.wali-kelas.destroy', $assignedWali->id));

        $response->assertRedirect(route('admin.wali-kelas.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('wali_kelas', ['id' => $assignedWali->id]);
    }

    public function test_wali_kelas_unassigned_can_be_deleted_by_admin(): void
    {
        $user = User::create([
            'name' => 'Wali Kelas Bebas Tugas',
            'email' => 'wali.bebas@example.com',
            'password' => bcrypt('password123'),
            'role' => 'wali_kelas',
            'is_active' => true,
        ]);
        $user->assignRole('wali_kelas');

        $wali = WaliKelas::create([
            'user_id' => $user->id,
            'nama' => 'Wali Kelas Bebas Tugas',
            'nip' => 'NIP-BEBAS-001',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.wali-kelas.destroy', $wali->id));

        $response->assertRedirect(route('admin.wali-kelas.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('wali_kelas', ['id' => $wali->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
