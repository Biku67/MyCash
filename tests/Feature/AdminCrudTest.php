<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Bendahara;
use App\Models\WaliKelas;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $bendaharaUser;
    protected $waliKelasUser;
    protected $siswaUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@mycash.app')->first();
        $this->bendaharaUser = User::where('email', 'bendahara@mycash.app')->first();
        $this->waliKelasUser = User::where('email', 'walikelas@mycash.app')->first();
        $this->siswaUser = User::where('email', 'budi@siswa.app')->first();
    }

    public function test_admin_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Super Admin');
    }

    public function test_non_admin_cannot_view_admin_dashboard(): void
    {
        if ($this->bendaharaUser) {
            $response = $this->actingAs($this->bendaharaUser)->get(route('admin.dashboard'));
            $response->assertStatus(403);
        }
    }

    public function test_admin_can_view_bendahara_index_and_datatable(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.bendahara.index'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Bendahara');

        $ajaxResponse = $this->actingAs($this->admin)->getJson(route('admin.bendahara.index'), [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJsonStructure(['data']);
    }

    public function test_admin_can_create_and_store_bendahara(): void
    {
        $kelas = Kelas::first();
        $this->assertNotNull($kelas, 'A class must exist for assigning bendahara');

        $response = $this->actingAs($this->admin)->get(route('admin.bendahara.create'));
        $response->assertStatus(200);

        $uniqueEmail = 'test_bendahara_' . time() . '@example.com';
        $postResponse = $this->actingAs($this->admin)->post(route('admin.bendahara.store'), [
            'name'                  => 'Test Bendahara Baru',
            'email'                 => $uniqueEmail,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'kode_kelas'            => $kelas->kode_kelas,
            'nis'                   => '12345678',
            'no_hp'                 => '081234567890',
        ]);

        $postResponse->assertRedirect(route('admin.bendahara.index'));
        $this->assertDatabaseHas('users', ['email' => $uniqueEmail, 'role' => 'bendahara']);
        $this->assertDatabaseHas('bendahara', ['nis' => '12345678', 'kode_kelas' => $kelas->kode_kelas]);
    }

    public function test_admin_can_view_kelas_index_and_datatable(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.kelas.index'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Master Kelas');

        $ajaxResponse = $this->actingAs($this->admin)->getJson(route('admin.kelas.index'), [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJsonStructure(['data']);
    }

    public function test_admin_can_create_and_store_kelas(): void
    {
        $uniqueCode = 'TEST-' . strtoupper(substr(md5(uniqid()), 0, 5));

        $response = $this->actingAs($this->admin)->get(route('admin.kelas.create'));
        $response->assertStatus(200);

        $postResponse = $this->actingAs($this->admin)->post(route('admin.kelas.store'), [
            'kode_kelas'      => $uniqueCode,
            'nama_kelas'      => 'Kelas Uji Coba CRUD',
            'tipe_periode'    => 'bulanan',
            'nominal_standar' => 25000,
        ]);

        $postResponse->assertRedirect(route('admin.kelas.index'));
        $this->assertDatabaseHas('kelas', [
            'kode_kelas'      => $uniqueCode,
            'nominal_standar' => 25000
        ]);
    }

    public function test_admin_can_create_kelas_with_auto_generated_kode(): void
    {
        $postResponse = $this->actingAs($this->admin)->post(route('admin.kelas.store'), [
            'nama_kelas'      => 'XII Rekayasa Perangkat Lunak 2',
            'tipe_periode'    => 'bulanan',
            'nominal_standar' => 20000,
        ]);

        $postResponse->assertRedirect(route('admin.kelas.index'));
        $this->assertDatabaseHas('kelas', [
            'kode_kelas' => 'XII-RPL-2',
            'nama_kelas' => 'XII Rekayasa Perangkat Lunak 2',
        ]);
    }

    public function test_admin_can_create_kelas_with_auto_generated_kode_when_collision_exists(): void
    {
        $this->actingAs($this->admin)->post(route('admin.kelas.store'), [
            'nama_kelas'      => 'XII Rekayasa Perangkat Lunak 3',
            'tipe_periode'    => 'bulanan',
            'nominal_standar' => 20000,
        ]);
        $this->assertDatabaseHas('kelas', ['kode_kelas' => 'XII-RPL-3']);

        $res = $this->actingAs($this->admin)->post(route('admin.kelas.store'), [
            'nama_kelas'      => 'XII Rekayasa Perangkat Lunak 3',
            'tipe_periode'    => 'bulanan',
            'nominal_standar' => 20000,
        ]);
        $res->assertRedirect(route('admin.kelas.index'));
        $this->assertDatabaseHas('kelas', ['kode_kelas' => 'XII-RPL-3-2']);
    }

    public function test_admin_can_update_kelas_and_cascade_kode_kelas(): void
    {
        $kelas = Kelas::where('kode_kelas', 'XII-RPL-1')->first();
        $this->assertNotNull($kelas);

        $newCode = 'XII-RPL-1-NEW';

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.kelas.update', $kelas->id), [
            'kode_kelas'      => $newCode,
            'nama_kelas'      => 'XII RPL 1 Updated',
            'tipe_periode'    => 'bulanan',
            'nominal_standar' => 30000,
        ]);

        $updateResponse->assertRedirect(route('admin.kelas.index'));
        $this->assertDatabaseHas('kelas', ['kode_kelas' => $newCode]);
        $this->assertDatabaseHas('siswa', ['kode_kelas' => $newCode]);
        $this->assertDatabaseHas('bendahara', ['kode_kelas' => $newCode]);
    }

    public function test_kelas_generate_kode_kelas_helper(): void
    {
        $this->assertEquals('XII-RPL-1', Kelas::generateKodeKelas('XII RPL 1'));
        $this->assertEquals('XII-RPL-1', Kelas::generateKodeKelas('XII Rekayasa Perangkat Lunak 1'));
        $this->assertEquals('X-TKJ-2', Kelas::generateKodeKelas('X Teknik Komputer dan Jaringan 2'));
        $this->assertEquals('10-MIPA-1', Kelas::generateKodeKelas('10 MIPA 1'));
        $this->assertEquals('VII-A', Kelas::generateKodeKelas('VII A'));
    }

    public function test_admin_can_view_wali_kelas_index_and_datatable(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.wali-kelas.index'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Wali Kelas');

        $ajaxResponse = $this->actingAs($this->admin)->getJson(route('admin.wali-kelas.index'), [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJsonStructure(['data']);
    }

    public function test_admin_can_create_and_store_wali_kelas(): void
    {
        $uniqueEmail = 'test_wali_' . time() . '@example.com';
        $uniqueNip = 'NIP-' . time();

        $response = $this->actingAs($this->admin)->get(route('admin.wali-kelas.create'));
        $response->assertStatus(200);

        $postResponse = $this->actingAs($this->admin)->post(route('admin.wali-kelas.store'), [
            'name'                  => 'Dra. Siti Aminah, M.Pd',
            'email'                 => $uniqueEmail,
            'nip'                   => $uniqueNip,
            'no_hp'                 => '08987654321',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $postResponse->assertRedirect(route('admin.wali-kelas.index'));
        $this->assertDatabaseHas('users', ['email' => $uniqueEmail, 'role' => 'wali_kelas']);
        $this->assertDatabaseHas('wali_kelas', ['nip' => $uniqueNip]);
    }

    public function test_all_role_dashboards_are_accessible(): void
    {
        if ($this->bendaharaUser) {
            $resp = $this->actingAs($this->bendaharaUser)->get(route('bendahara.dashboard'));
            $resp->assertStatus(200);
            $resp->assertSee('Selamat Datang!');
        }

        if ($this->waliKelasUser) {
            $resp = $this->actingAs($this->waliKelasUser)->get(route('wali-kelas.dashboard'));
            $resp->assertStatus(200);
            $resp->assertSee('Selamat Datang!');
        }

        if ($this->siswaUser) {
            $resp = $this->actingAs($this->siswaUser)->get(route('siswa.dashboard'));
            $resp->assertStatus(200);
            $resp->assertSee('Selamat Datang!');
            $resp->assertSee('NIS: ' . $this->siswaUser->siswa->nis);
        }
    }
}
