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

class FlexiblePeriodTest extends TestCase
{
    use RefreshDatabase;

    protected $bendaharaUser;
    protected $bendahara;
    protected $studentUser;
    protected $student;
    protected $kelas;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->bendaharaUser = User::where('email', 'bendahara@mycash.app')->first();
        $this->bendahara = $this->bendaharaUser->bendahara;
        $this->kelas = $this->bendahara->kelas;

        $this->studentUser = User::where('email', 'budi@siswa.app')->first();
        $this->student = $this->studentUser->siswa;
    }

    public function test_bendahara_can_update_fee_settings_with_same_year_month_range(): void
    {
        $response = $this->actingAs($this->bendaharaUser)->post(route('bendahara.students.updateFeeSettings'), [
            'fee_period_type' => 'bulanan',
            'start_month' => 'Jul',
            'end_month' => 'Des',
            'fee_amount' => 15000,
        ]);

        $response->assertRedirect(route('bendahara.students.index'));
        $response->assertSessionHas('success');

        $this->kelas->refresh();
        $this->assertEquals('bulanan', $this->kelas->tipe_periode);
        $this->assertEquals('Jul', $this->kelas->bulan_mulai);
        $this->assertEquals('Des', $this->kelas->bulan_selesai);
        $this->assertEquals(15000, (int)$this->kelas->nominal_standar);

        $expectedPeriods = ['Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
        $this->assertEquals($expectedPeriods, $this->kelas->getPeriods());
        $this->assertCount(6, $this->kelas->getPeriods());

        // Verify index view renders the 6 periods
        $indexResponse = $this->actingAs($this->bendaharaUser)->get(route('bendahara.students.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Jul - Des · 6 Bulan');
        foreach ($expectedPeriods as $p) {
            $indexResponse->assertSee($p);
        }
    }

    public function test_bendahara_can_update_fee_settings_with_cross_year_month_range(): void
    {
        $response = $this->actingAs($this->bendaharaUser)->post(route('bendahara.students.updateFeeSettings'), [
            'fee_period_type' => 'bulanan',
            'start_month' => 'Jul',
            'end_month' => 'Jun',
            'fee_amount' => 20000,
        ]);

        $response->assertRedirect(route('bendahara.students.index'));

        $this->kelas->refresh();
        $this->assertEquals('Jul', $this->kelas->bulan_mulai);
        $this->assertEquals('Jun', $this->kelas->bulan_selesai);

        $expectedPeriods = ['Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
        $this->assertEquals($expectedPeriods, $this->kelas->getPeriods());
        $this->assertCount(12, $this->kelas->getPeriods());
        $this->assertEquals('Jul', $this->kelas->getPeriods()[0]);
        $this->assertEquals('Jun', end($expectedPeriods));
    }

    public function test_debt_and_target_are_calculated_based_on_active_period_count(): void
    {
        // 6 months (Jul - Des), 10,000 per month => total target = 60,000
        $this->kelas->update([
            'tipe_periode' => 'bulanan',
            'bulan_mulai' => 'Jul',
            'bulan_selesai' => 'Des',
            'nominal_standar' => 10000,
        ]);

        // Student has no payments yet => debt is 60,000
        $response = $this->actingAs($this->bendaharaUser)->get(route('bendahara.students.index'));
        $response->assertStatus(200);

        $students = $response->viewData('students');
        $studentInView = $students->firstWhere('id', $this->student->id);
        $this->assertNotNull($studentInView);
        // Student had seeded payments, let's verify outstanding_debt = max(0, 6 * 10000 - contributed)
        $expectedDebt = max(0.0, (6 * 10000) - (float)$studentInView->contributed);
        $this->assertEquals($expectedDebt, $studentInView->outstanding_debt);
    }

    public function test_siswa_history_displays_dynamic_periods(): void
    {
        $this->kelas->update([
            'tipe_periode' => 'bulanan',
            'bulan_mulai' => 'Agt',
            'bulan_selesai' => 'Des',
            'nominal_standar' => 25000,
        ]);

        $response = $this->actingAs($this->studentUser)->get(route('siswa.history.index'));
        $response->assertStatus(200);
        $response->assertSee('Bulanan (Agt - Des · 5 Bulan)');
        $response->assertSee('5 Periode');
    }

    public function test_transaction_auto_allocation_uses_custom_start_month(): void
    {
        // Set class period Jul - Des (fee 10.000)
        $this->kelas->update([
            'tipe_periode' => 'bulanan',
            'bulan_mulai' => 'Jul',
            'bulan_selesai' => 'Des',
            'nominal_standar' => 10000,
        ]);

        // Delete existing transactions for clean testing
        DetailTransaksiKas::where('id_siswa', $this->student->id)->delete();

        $response = $this->actingAs($this->bendaharaUser)->post(route('bendahara.transactions.store'), [
            'type' => 'income',
            'category' => 'Uang Kas',
            'amount' => 20000,
            'description' => 'Iuran Kas Budi Semester 1',
            'transaction_date' => now()->toDateString(),
            'student_id' => $this->student->id,
        ]);

        $response->assertRedirect(route('bendahara.transactions.index'));

        // Check details allocated: should allocate to 'Jul' and 'Agt', NOT 'Jan' and 'Feb'
        $details = DetailTransaksiKas::where('id_siswa', $this->student->id)->get();
        $this->assertCount(2, $details);

        $allocatedPeriods = $details->pluck('periode')->toArray();
        $this->assertContains('Jul', $allocatedPeriods);
        $this->assertContains('Agt', $allocatedPeriods);
        $this->assertNotContains('Jan', $allocatedPeriods);
        $this->assertNotContains('Feb', $allocatedPeriods);
    }

    public function test_export_excel_and_pdf_use_dynamic_periods(): void
    {
        $this->kelas->update([
            'tipe_periode' => 'bulanan',
            'bulan_mulai' => 'Jul',
            'bulan_selesai' => 'Des',
            'nominal_standar' => 15000,
        ]);

        // Test Excel Export
        $excelResponse = $this->actingAs($this->bendaharaUser)->get(route('bendahara.students.export'));
        $excelResponse->assertStatus(200);

        // Test PDF Export
        $pdfResponse = $this->actingAs($this->bendaharaUser)->get(route('bendahara.students.exportPdf'));
        $pdfResponse->assertStatus(200);
    }

    public function test_reset_matrix_clears_checklist_without_deleting_history(): void
    {
        $kategori = Kategori::firstOrCreate([
            'nama_kategori' => 'Uang Kas',
            'tipe' => 'pemasukan',
        ]);

        $transaksi = TransaksiKas::create([
            'kode_kelas' => $this->kelas->kode_kelas,
            'id_bendahara' => $this->bendahara->id,
            'id_kategori' => $kategori->id,
            'jenis_transaksi' => 'pemasukan',
            'total_nominal' => 20000,
            'tanggal_transaksi' => now()->toDateString(),
            'keterangan' => 'Kas Jul',
        ]);

        DetailTransaksiKas::create([
            'id_transaksi_kas' => $transaksi->id,
            'id_siswa' => $this->student->id,
            'periode' => 'Jul',
            'nominal' => 20000,
        ]);

        // Before reset: paid_periods contains 'Jul'
        $response1 = $this->actingAs($this->bendaharaUser)->get(route('bendahara.students.index'));
        $student1 = $response1->viewData('students')->firstWhere('id', $this->student->id);
        $this->assertContains('Jul', $student1->paid_periods);

        // Action: Reset Matrix
        sleep(1);
        $resetResponse = $this->actingAs($this->bendaharaUser)->post(route('bendahara.students.resetMatrix'));
        $resetResponse->assertRedirect(route('bendahara.students.index'));

        // After reset: paid_periods is empty
        $response2 = $this->actingAs($this->bendaharaUser)->get(route('bendahara.students.index'));
        $student2 = $response2->viewData('students')->firstWhere('id', $this->student->id);
        $this->assertEmpty($student2->paid_periods);

        // Check history / transactions: still exists!
        $this->assertDatabaseHas('transaksi_kas', ['id' => $transaksi->id]);
    }
}
