<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaliKelasChecklistTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_wali_kelas_can_access_checklist_page(): void
    {
        $waliUser = User::where('role', 'wali_kelas')->first();
        $this->assertNotNull($waliUser);

        $response = $this->actingAs($waliUser)->get(route('wali-kelas.checklist.index'));
        $response->assertStatus(200);
        $response->assertSee('Checklist Kas Siswa');
        $response->assertSee('Ketentuan Iuran Kelas');
    }
}
