<?php

namespace Tests\Feature;

use App\Models\Bendahara;
use App\Models\Kelas;
use App\Models\TransaksiKas;
use App\Models\Kategori;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BendaharaReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_bendahara_can_access_report_page_and_see_summary(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $this->assertNotNull($bendaharaUser);

        $bendahara = $bendaharaUser->bendahara;
        $this->assertNotNull($bendahara);

        $response = $this->actingAs($bendaharaUser)->get(route('bendahara.report.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Keuangan Kas');
        $response->assertSee('Total Pemasukan');
        $response->assertSee('Total Pengeluaran');
        $response->assertSee('Saldo Kas Akhir');
        $response->assertSee('Buku Mutasi Kas');
        $response->assertSee('datepicker-report');
        $response->assertSee('max="' . date('Y-m-d') . '"', false);
        $response->assertSee('flatpickr');
    }

    public function test_bendahara_can_filter_report_by_date_range(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $bendahara = $bendaharaUser->bendahara;

        $kategori = Kategori::firstOrCreate(
            ['nama_kategori' => 'Uang Kas', 'tipe' => 'pemasukan']
        );

        // Transaction inside filter
        TransaksiKas::create([
            'kode_kelas' => $bendahara->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'id_kategori' => $kategori->id,
            'jenis_transaksi' => 'pemasukan',
            'total_nominal' => 50000,
            'tanggal_transaksi' => '2026-05-15',
            'keterangan' => 'Kas Bulan Mei',
        ]);

        $response = $this->actingAs($bendaharaUser)->get(route('bendahara.report.index', [
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-31',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Kas Bulan Mei');
        $response->assertSee('50.000');
    }

    public function test_bendahara_can_download_excel_report(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();

        $response = $this->actingAs($bendaharaUser)->get(route('bendahara.report.exportExcel', [
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ]));

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-type'), 'spreadsheet') ||
            str_contains($response->headers->get('content-disposition'), '.xlsx')
        );
    }

    public function test_bendahara_can_download_pdf_report(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();

        $response = $this->actingAs($bendaharaUser)->get(route('bendahara.report.exportPdf', [
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_non_bendahara_cannot_access_report(): void
    {
        $waliUser = User::where('role', 'wali_kelas')->first();
        $this->assertNotNull($waliUser);

        $response = $this->actingAs($waliUser)->get(route('bendahara.report.index'));
        $response->assertStatus(403);
    }

    public function test_chart_renders_apexcharts_and_handles_month_stepping(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();

        $response = $this->actingAs($bendaharaUser)->get(route('bendahara.report.index', [
            'start_date' => '2026-01-31',
            'end_date'   => '2026-03-15',
        ]));

        $response->assertStatus(200);
        $response->assertSee('id="cashFlowChart"', false);
        $response->assertSee('new ApexCharts', false);
        $response->assertSee('setPreset');
        $response->assertViewHas('months', function ($months) {
            return count($months) === 3; // Jan, Feb, Mar 2026 without skipping February
        });
    }
}
