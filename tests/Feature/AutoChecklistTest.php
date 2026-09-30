<?php

namespace Tests\Feature;

use App\Models\Bendahara;
use App\Models\DetailTransaksiKas;
use App\Models\Kelas;
use App\Models\LogTransaksiKas;
use App\Models\Siswa;
use App\Models\TransaksiKas;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoChecklistTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_transaction_creation_triggers_auto_checklist_periods(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $this->assertNotNull($bendaharaUser);

        $bendahara = $bendaharaUser->bendahara;
        $kelas = $bendahara->kelas;
        $student = Siswa::where('kode_kelas', $kelas->kode_kelas)->first();
        $this->assertNotNull($student);

        // Clear existing details for clean test
        DetailTransaksiKas::where('id_siswa', $student->id)->delete();

        $standardFee = (float)($kelas->nominal_standar ?? 20000);
        $amount = $standardFee * 2; // covers 2 periods

        $response = $this->actingAs($bendaharaUser)->post(route('bendahara.transactions.store'), [
            'type' => 'income',
            'amount' => $amount,
            'description' => 'Pembayaran Uang Kas Test',
            'transaction_date' => now()->format('Y-m-d'),
            'category' => 'Uang Kas',
            'student_id' => $student->id,
        ]);

        $response->assertRedirect(route('bendahara.transactions.index'));

        // Assert 2 details were auto-created
        $details = DetailTransaksiKas::where('id_siswa', $student->id)->get();
        $this->assertCount(2, $details);

        // Expected periods: first two periods ('Jan', 'Feb' or 'M1', 'M2')
        $expectedPeriod1 = $kelas->tipe_periode === 'mingguan' ? 'M1' : 'Jan';
        $expectedPeriod2 = $kelas->tipe_periode === 'mingguan' ? 'M2' : 'Feb';

        $this->assertTrue($details->pluck('periode')->contains($expectedPeriod1));
        $this->assertTrue($details->pluck('periode')->contains($expectedPeriod2));

        // Check transaction keterangan updated with period names
        $tx = TransaksiKas::where('kode_kelas', $kelas->kode_kelas)->latest('id')->first();
        $this->assertStringContainsString("({$expectedPeriod1}, {$expectedPeriod2})", $tx->keterangan);
    }

    public function test_matrix_checkbox_toggle_absensi_creates_and_deletes_transaction(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $bendahara = $bendaharaUser->bendahara;
        $kelas = $bendahara->kelas;
        $student = Siswa::where('kode_kelas', $kelas->kode_kelas)->first();

        // 1. Toggle ON (Checked)
        $toggleOnResp = $this->actingAs($bendaharaUser)->postJson(route('bendahara.students.toggleAbsensi'), [
            'student_id' => $student->id,
            'period' => 'Des',
            'status' => 1,
        ]);

        $toggleOnResp->assertStatus(200);
        $toggleOnResp->assertJson(['success' => true]);

        $this->assertDatabaseHas('detail_transaksi_kas', [
            'id_siswa' => $student->id,
            'periode' => 'Des',
        ]);

        // 2. Toggle OFF (Unchecked)
        $toggleOffResp = $this->actingAs($bendaharaUser)->postJson(route('bendahara.students.toggleAbsensi'), [
            'student_id' => $student->id,
            'period' => 'Des',
            'status' => 0,
        ]);

        $toggleOffResp->assertStatus(200);
        $toggleOffResp->assertJson(['success' => true]);

        $this->assertDatabaseMissing('detail_transaksi_kas', [
            'id_siswa' => $student->id,
            'periode' => 'Des',
        ]);
    }

    public function test_partial_payment_and_installment_completion_triggers_auto_checklist(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $bendahara = $bendaharaUser->bendahara;
        $kelas = $bendahara->kelas;
        $student = Siswa::where('kode_kelas', $kelas->kode_kelas)->first();

        // Clear details for clean state
        DetailTransaksiKas::where('id_siswa', $student->id)->delete();

        // Set standard fee to 10,000 for clear math
        $kelas->update(['nominal_standar' => 10000, 'tipe_periode' => 'bulanan']);

        // STEP 1: Pay 5,000 (below nominal standar of 10,000)
        $resp1 = $this->actingAs($bendaharaUser)->post(route('bendahara.transactions.store'), [
            'type' => 'income',
            'amount' => 5000,
            'description' => 'Bayar kas Budi cicil 1',
            'transaction_date' => now()->format('Y-m-d'),
            'category' => 'Uang Kas',
            'student_id' => $student->id,
        ]);
        $resp1->assertRedirect(route('bendahara.transactions.index'));

        // Check index view: Total dibayar must show 5,000, but 'Jan' is NOT yet checked
        $viewResp1 = $this->actingAs($bendaharaUser)->get(route('bendahara.students.index'));
        $viewResp1->assertStatus(200);
        $students1 = $viewResp1->viewData('students');
        $studentData1 = $students1->where('id', $student->id)->first();

        $this->assertEquals(5000, $studentData1->contributed, 'Total Bayar must reflect 5,000 even though below standard fee');
        $this->assertNotContains('Jan', $studentData1->paid_periods, 'Jan should not be fully checked yet');
        $this->assertEquals(5000, $studentData1->partial_periods['Jan'] ?? 0, 'Jan should have partial 5,000 recorded');

        // STEP 2: Pay another 5,000 (5,000 + 5,000 = 10,000, completing 'Jan')
        $resp2 = $this->actingAs($bendaharaUser)->post(route('bendahara.transactions.store'), [
            'type' => 'income',
            'amount' => 5000,
            'description' => 'Bayar kas Budi cicil 2',
            'transaction_date' => now()->format('Y-m-d'),
            'category' => 'Uang Kas',
            'student_id' => $student->id,
        ]);
        $resp2->assertRedirect(route('bendahara.transactions.index'));

        // Check index view: Total dibayar must show 10,000, and 'Jan' IS NOW AUTO CHECKLISTED!
        $viewResp2 = $this->actingAs($bendaharaUser)->get(route('bendahara.students.index'));
        $viewResp2->assertStatus(200);
        $students2 = $viewResp2->viewData('students');
        $studentData2 = $students2->where('id', $student->id)->first();

        $this->assertEquals(10000, $studentData2->contributed, 'Total Bayar must now reflect 10,000');
        $this->assertContains('Jan', $studentData2->paid_periods, 'Jan must now be AUTO CHECKLISTED!');
        $this->assertArrayNotHasKey('Jan', $studentData2->partial_periods, 'Jan is fully paid, so not partial anymore');
    }

    public function test_transaction_date_cannot_be_in_the_future(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $student = Siswa::where('kode_kelas', $bendaharaUser->bendahara->kode_kelas)->first();

        // 1. Attempt to store transaction with future date
        $futureDate = now()->addDays(2)->format('Y-m-d');
        $response = $this->actingAs($bendaharaUser)->post(route('bendahara.transactions.store'), [
            'type' => 'income',
            'amount' => 20000,
            'description' => 'Transaksi Masa Depan',
            'transaction_date' => $futureDate,
            'category' => 'Uang Kas',
            'student_id' => $student->id,
        ]);

        $response->assertSessionHasErrors(['transaction_date']);
        $this->assertEquals(
            'Tanggal transaksi tidak boleh melebihi hari ini.',
            session('errors')->first('transaction_date')
        );

        // 2. Verify max attribute exists on create view
        $createView = $this->actingAs($bendaharaUser)->get(route('bendahara.transactions.create'));
        $createView->assertStatus(200);
        $createView->assertSee('max="' . date('Y-m-d') . '"', false);
    }

    public function test_transaction_date_can_be_past_date(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $student = Siswa::where('kode_kelas', $bendaharaUser->bendahara->kode_kelas)->first();

        // Storing backdated transaction (e.g. 3 days ago) should succeed
        $pastDate = now()->subDays(3)->format('Y-m-d');
        $response = $this->actingAs($bendaharaUser)->post(route('bendahara.transactions.store'), [
            'type' => 'income',
            'amount' => 20000,
            'description' => 'Setoran Kas Minggu Lalu',
            'transaction_date' => $pastDate,
            'category' => 'Uang Kas',
            'student_id' => $student->id,
        ]);

        $response->assertRedirect(route('bendahara.transactions.index'));
        $tx = TransaksiKas::where('kode_kelas', $bendaharaUser->bendahara->kode_kelas)->latest('id')->first();
        $this->assertNotNull($tx);
        $this->assertStringContainsString('Setoran Kas Minggu Lalu', $tx->keterangan);
        $this->assertEquals($pastDate, $tx->tanggal_transaksi->format('Y-m-d'));
    }
}
