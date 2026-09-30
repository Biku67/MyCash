<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprehensiveExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_wali_kelas_report_exports(): void
    {
        $wali = User::where('role', 'wali_kelas')->first();
        $this->assertNotNull($wali);

        // PDF Stream / Preview
        $pdfStream = $this->actingAs($wali)->get(route('wali-kelas.report.exportPdf'));
        $pdfStream->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfStream->headers->get('content-type'));

        // PDF Download
        $pdfDl = $this->actingAs($wali)->get(route('wali-kelas.report.exportPdf', ['download' => '1']));
        $pdfDl->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfDl->headers->get('content-type'));

        // Excel Download
        $excel = $this->actingAs($wali)->get(route('wali-kelas.report.exportExcel'));
        $excel->assertStatus(200);
        $this->assertTrue(
            str_contains($excel->headers->get('content-type'), 'spreadsheet') ||
            str_contains($excel->headers->get('content-disposition'), '.xlsx')
        );
    }

    public function test_bendahara_report_exports(): void
    {
        $bendahara = User::where('role', 'bendahara')->first();
        $this->assertNotNull($bendahara);

        $pdf = $this->actingAs($bendahara)->get(route('bendahara.report.exportPdf'));
        $pdf->assertStatus(200);
        $this->assertEquals('application/pdf', $pdf->headers->get('content-type'));

        $excel = $this->actingAs($bendahara)->get(route('bendahara.report.exportExcel'));
        $excel->assertStatus(200);
        $this->assertTrue(
            str_contains($excel->headers->get('content-type'), 'spreadsheet') ||
            str_contains($excel->headers->get('content-disposition'), '.xlsx')
        );
    }

    public function test_only_report_pages_have_export_buttons(): void
    {
        $bendahara = User::where('role', 'bendahara')->first();
        $this->assertNotNull($bendahara);

        // Transactions page should NOT have export buttons
        $txResponse = $this->actingAs($bendahara)->get(route('bendahara.transactions.index'));
        $txResponse->assertStatus(200);
        $txResponse->assertDontSee('transaksi.export');
        $txResponse->assertDontSee('Unduh Riwayat Transaksi Format Excel');

        // Students page should NOT have export buttons
        $studentResponse = $this->actingAs($bendahara)->get(route('bendahara.students.index'));
        $studentResponse->assertStatus(200);
        $studentResponse->assertDontSee('students.export');
        $studentResponse->assertDontSee('Unduh Status Pembayaran Siswa Format Excel');

        // Report page MUST have export buttons
        $reportResponse = $this->actingAs($bendahara)->get(route('bendahara.report.index'));
        $reportResponse->assertStatus(200);
        $reportResponse->assertSee(route('bendahara.report.exportExcel'));
        $reportResponse->assertSee(route('bendahara.report.exportPdf'));

        // Wali Kelas report page MUST have export buttons
        $wali = User::where('role', 'wali_kelas')->first();
        $waliReportResponse = $this->actingAs($wali)->get(route('wali-kelas.report.index'));
        $waliReportResponse->assertStatus(200);
        $waliReportResponse->assertSee(route('wali-kelas.report.exportExcel'));
        $waliReportResponse->assertSee(route('wali-kelas.report.exportPdf'));
    }
}
