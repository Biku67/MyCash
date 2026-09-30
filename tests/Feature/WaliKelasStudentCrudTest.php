<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use App\Models\WaliKelas;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaliKelasStudentCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_wali_kelas_can_view_student_list_and_ajax_datatable(): void
    {
        $waliUser = User::where('role', 'wali_kelas')->first();
        $this->assertNotNull($waliUser);

        $waliKelas = $waliUser->waliKelas;
        $this->assertNotNull($waliKelas);

        $kelas = $waliKelas->kelas()->first();
        $this->assertNotNull($kelas);

        // Standard GET request
        $response = $this->actingAs($waliUser)->get(route('wali-kelas.students.index'));
        $response->assertStatus(200);
        $response->assertSee($kelas->nama_kelas);

        // AJAX DataTables request
        $ajaxResponse = $this->actingAs($waliUser)->get(route('wali-kelas.students.index'), [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJsonStructure(['data']);
    }

    public function test_wali_kelas_can_create_student_and_user_account(): void
    {
        $waliUser = User::where('role', 'wali_kelas')->first();
        $kelas = $waliUser->waliKelas->kelas()->first();

        // Pendaftaran siswa tanpa input password (password otomatis diisi dari NIS)
        $studentData = [
            'name' => 'Bintang Pratama',
            'nis' => '99887766',
            'email' => 'bintang.pratama@example.com',
            'phone' => '081234567890',
        ];

        $response = $this->actingAs($waliUser)->post(route('wali-kelas.students.store'), $studentData);
        $response->assertRedirect(route('wali-kelas.students.index'));
        $response->assertSessionHas('success');

        // Verify siswa record
        $this->assertDatabaseHas('siswa', [
            'nama' => 'Bintang Pratama',
            'nis' => '99887766',
            'kode_kelas' => $kelas->kode_kelas,
            'no_hp' => '081234567890',
        ]);

        // Verify user account
        $user = User::where('email', 'bintang.pratama@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Bintang Pratama', $user->name);
        $this->assertEquals('siswa', $user->role);

        // Verify that user password matches NIS
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('99887766', $user->password));

        // Verify student can log in using their NIS as password
        $this->assertTrue(\Illuminate\Support\Facades\Auth::attempt([
            'email' => 'bintang.pratama@example.com',
            'password' => '99887766',
        ]));
    }

    public function test_wali_kelas_can_edit_and_update_student(): void
    {
        $waliUser = User::where('role', 'wali_kelas')->first();
        $kelas = $waliUser->waliKelas->kelas()->first();

        $student = Siswa::where('kode_kelas', $kelas->kode_kelas)->first();
        $this->assertNotNull($student);

        // View edit page
        $editPage = $this->actingAs($waliUser)->get(route('wali-kelas.students.edit', $student->id));
        $editPage->assertStatus(200);
        $editPage->assertSee($student->nama);

        // Update student
        $updateData = [
            'name' => 'Nama Siswa Diperbarui',
            'nis' => $student->nis,
            'email' => 'updated.student@example.com',
            'phone' => '08999998888',
        ];

        $updateResponse = $this->actingAs($waliUser)->put(route('wali-kelas.students.update', $student->id), $updateData);
        $updateResponse->assertRedirect(route('wali-kelas.students.index'));
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('siswa', [
            'id' => $student->id,
            'nama' => 'Nama Siswa Diperbarui',
            'no_hp' => '08999998888',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $student->user_id,
            'name' => 'Nama Siswa Diperbarui',
            'email' => 'updated.student@example.com',
        ]);
    }

    public function test_wali_kelas_can_delete_student(): void
    {
        $waliUser = User::where('role', 'wali_kelas')->first();
        $kelas = $waliUser->waliKelas->kelas()->first();

        // Create a temporary student to delete
        $user = User::create([
            'name' => 'Siswa Dihapus',
            'email' => 'hapus@siswa.mycash.id',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);
        $user->assignRole('siswa');

        $student = Siswa::create([
            'user_id' => $user->id,
            'kode_kelas' => $kelas->kode_kelas,
            'nama' => 'Siswa Dihapus',
            'nis' => '1122334455',
        ]);

        $deleteResponse = $this->actingAs($waliUser)->delete(route('wali-kelas.students.destroy', $student->id));
        $deleteResponse->assertRedirect(route('wali-kelas.students.index'));
        $deleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('siswa', ['id' => $student->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_bendahara_cannot_access_student_create_route_anymore(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();

        // Old route does not exist -> 404
        $response = $this->actingAs($bendaharaUser)->get('/bendahara/students/create');
        $response->assertStatus(404);

        // Checklist kas is still accessible
        $checklistResponse = $this->actingAs($bendaharaUser)->get(route('bendahara.students.index'));
        $checklistResponse->assertStatus(200);
    }

    public function test_bendahara_cannot_access_wali_kelas_students_management(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();

        $response = $this->actingAs($bendaharaUser)->get(route('wali-kelas.students.index'));
        $response->assertStatus(403);
    }

    public function test_wali_kelas_can_reset_student_password_to_nis(): void
    {
        $waliUser = User::where('role', 'wali_kelas')->first();
        $kelas = $waliUser->waliKelas->kelas()->first();

        $student = Siswa::where('kode_kelas', $kelas->kode_kelas)->first();
        $this->assertNotNull($student);

        // Change password to a custom string first
        $student->user->update([
            'password' => \Illuminate\Support\Facades\Hash::make('customOldPassword123'),
        ]);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('customOldPassword123', $student->user->password));

        // Reset password to NIS
        $resetResponse = $this->actingAs($waliUser)->post(route('wali-kelas.students.resetPassword', $student->id));
        $resetResponse->assertRedirect();
        $resetResponse->assertSessionHas('success');

        // Refresh user and verify password is now the NIS
        $student->user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check($student->nis, $student->user->password));
    }
}
