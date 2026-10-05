<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\TransaksiKas;
use App\Models\Kategori;
use App\Models\User;
use App\Models\WaliKelas;
use App\Models\Bendahara;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaliKelasReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_wali_kelas_can_access_report_page_and_see_summary(): void
    {
        $waliUser = User::where('role', 'wali_kelas')->first();
        $this->assertNotNull($waliUser);

        $response = $this->actingAs($waliUser)->get(route('wali-kelas.report.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Keuangan Kas');
        $response->assertSee('Total Pemasukan');
        $response->assertSee('Total Pengeluaran');
        $response->assertSee('Saldo Akhir');
        $response->assertSee('Rincian Mutasi Kas');
        $response->assertSee('datepicker-report');
        $response->assertSee('max="' . date('Y-m-d') . '"', false);
        $response->assertSee('flatpickr');
    }

    public function test_wali_kelas_can_filter_report_by_date_range_and_category(): void
    {
        $waliUser = User::where('role', 'wali_kelas')->first();
        $waliKelas = $waliUser->waliKelas;
        $kelas = $waliKelas->kelas()->first();
        $this->assertNotNull($kelas);

        $bendahara = $kelas->bendahara->first();
        $this->assertNotNull($bendahara);

        $kategori = Kategori::firstOrCreate(
            ['nama_kategori' => 'Uang Kas', 'tipe' => 'pemasukan']
        );

        // Transaction inside date range
        TransaksiKas::create([
            'kode_kelas' => $kelas->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'id_kategori' => $kategori->id,
            'jenis_transaksi' => 'pemasukan',
            'total_nominal' => 75000,
            'tanggal_transaksi' => '2026-06-10',
            'keterangan' => 'Kas Semester Genap',
        ]);

        $response = $this->actingAs($waliUser)->get(route('wali-kelas.report.index', [
            'kode_kelas' => $kelas->kode_kelas,
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30',
            'category_id' => $kategori->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Kas Semester Genap');
        $response->assertSee('75.000');
    }

    public function test_wali_kelas_can_download_excel_report(): void
    {
        $waliUser = User::where('role', 'wali_kelas')->first();

        $response = $this->actingAs($waliUser)->get(route('wali-kelas.report.exportExcel', [
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ]));

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-type'), 'spreadsheet') ||
            str_contains($response->headers->get('content-disposition'), '.xlsx')
        );
    }

    public function test_wali_kelas_can_download_pdf_report(): void
    {
        $waliUser = User::where('role', 'wali_kelas')->first();

        $response = $this->actingAs($waliUser)->get(route('wali-kelas.report.exportPdf', [
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_siswa_cannot_access_wali_kelas_report(): void
    {
        $siswaUser = User::where('role', 'siswa')->first();
        $this->assertNotNull($siswaUser);

        $response = $this->actingAs($siswaUser)->get(route('wali-kelas.report.index'));
        $response->assertStatus(403);
    }

    public function test_wali_kelas_layout_contains_report_navigation_link(): void
    {
        $waliUser = User::where('role', 'wali_kelas')->first();
        $this->assertNotNull($waliUser);

        $response = $this->actingAs($waliUser)->get(route('wali-kelas.dashboard'));
        $response->assertStatus(200);
        $response->assertSee(route('wali-kelas.report.index'));
        $response->assertSee('Laporan Kas');
    }

    public function test_chart_renders_apexcharts_and_handles_month_stepping(): void
    {
        $waliUser = User::where('role', 'wali_kelas')->first();

        $response = $this->actingAs($waliUser)->get(route('wali-kelas.report.index', [
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
