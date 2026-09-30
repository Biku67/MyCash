<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Bendahara;
use App\Models\WaliKelas;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingAndChecklistEnhancementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_bendahara_views_contain_tour_trigger_and_checklist_cta(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $this->assertNotNull($bendaharaUser);

        $response = $this->actingAs($bendaharaUser)->get(route('bendahara.students.index'));
        $response->assertStatus(200);

        // Has help button for tour
        $response->assertSee('start-tour');
        $response->assertSee('Panduan Alur MyCash');

        // Has onboarding tour component
        $response->assertSee('onboardingTour');

        // Has automatic status message
        $response->assertSee('Status kas di bawah terisi <b>otomatis</b>', false);

        // Does NOT contain interactive checkbox-absensi input
        $response->assertDontSee('class="checkbox-absensi');
    }

    public function test_bendahara_multipage_tour_targets_exist(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $this->assertNotNull($bendaharaUser);

        // 1. Dashboard: Saldo Kas target
        $resDashboard = $this->actingAs($bendaharaUser)->get(route('bendahara.dashboard'));
        $resDashboard->assertStatus(200);
        $resDashboard->assertSee('id="tour-bendahara-stat-saldo"', false);

        // 2. Transactions: Create Tx Button target
        $resTx = $this->actingAs($bendaharaUser)->get(route('bendahara.transactions.index'));
        $resTx->assertStatus(200);
        $resTx->assertSee('id="tour-bendahara-btn-create-tx"', false);

        // 3. Create Transaction Form: Detail targets (type, category, student, amount, description, date)
        $resCreate = $this->actingAs($bendaharaUser)->get(route('bendahara.transactions.create'));
        $resCreate->assertStatus(200);
        $resCreate->assertSee('id="tour-bendahara-tx-type"', false);
        $resCreate->assertSee('id="tour-bendahara-tx-category"', false);
        $resCreate->assertSee('id="tour-bendahara-form-student"', false);
        $resCreate->assertSee('id="tour-bendahara-tx-amount"', false);
        $resCreate->assertSee('id="tour-bendahara-tx-description"', false);
        $resCreate->assertSee('id="tour-bendahara-tx-date"', false);

        // 4. Students: Detail Checklist targets (settings, banner, matrix, periods, paid, debt)
        $resStudents = $this->actingAs($bendaharaUser)->get(route('bendahara.students.index'));
        $resStudents->assertStatus(200);
        $resStudents->assertSee('id="tour-bendahara-btn-settings"', false);
        $resStudents->assertSee('id="tour-bendahara-students-table"', false);
        $resStudents->assertSee('id="tour-bendahara-checklist-matrix"', false);
        $resStudents->assertSee('id="tour-bendahara-col-periods"', false);
        $resStudents->assertSee('id="tour-bendahara-col-paid"', false);
        $resStudents->assertSee('id="tour-bendahara-col-debt"', false);

        // 5. Report: Filter, Stats, Chart, Table, and Actions targets
        $resReport = $this->actingAs($bendaharaUser)->get(route('bendahara.report.index'));
        $resReport->assertStatus(200);
        $resReport->assertSee('id="tour-bendahara-report-filter"', false);
        $resReport->assertSee('id="tour-bendahara-report-stats"', false);
        $resReport->assertSee('id="tour-bendahara-report-chart"', false);
        $resReport->assertSee('id="tour-bendahara-report-table"', false);
        $resReport->assertSee('id="tour-bendahara-report-actions"', false);

        // 6. Announcements: Index and Create targets
        $resAnnounce = $this->actingAs($bendaharaUser)->get(route('bendahara.announcements.index'));
        $resAnnounce->assertStatus(200);
        $resAnnounce->assertSee('id="tour-bendahara-btn-create-announcement"', false);
        $resAnnounce->assertSee('id="tour-bendahara-announcement-stats"', false);
        $resAnnounce->assertSee('id="tour-bendahara-announcement-list"', false);

        $resAnnounceCreate = $this->actingAs($bendaharaUser)->get(route('bendahara.announcements.create'));
        $resAnnounceCreate->assertStatus(200);
        $resAnnounceCreate->assertSee('id="tour-bendahara-announcement-target"', false);
        $resAnnounceCreate->assertSee('id="tour-bendahara-announcement-title"', false);
        $resAnnounceCreate->assertSee('id="tour-bendahara-announcement-body"', false);
        $resAnnounceCreate->assertSee('id="tour-bendahara-announcement-submit"', false);
    }

    public function test_siswa_views_contain_tour_trigger_and_status_badges(): void
    {
        $siswaUser = User::where('role', 'siswa')->first();
        $this->assertNotNull($siswaUser);

        $resDashboard = $this->actingAs($siswaUser)->get(route('siswa.dashboard'));
        $resDashboard->assertStatus(200);
        $resDashboard->assertSee('id="tour-siswa-stat-card"', false);

        $response = $this->actingAs($siswaUser)->get(route('siswa.history.index'));
        $response->assertStatus(200);

        // Has help button
        $response->assertSee('start-tour');
        $response->assertSee('Panduan Alur MyCash');

        // Has onboarding tour component
        $response->assertSee('onboardingTour');

        // Has target ID
        $response->assertSee('id="tour-siswa-history-matrix"', false);

        // Has automatic status notice
        $response->assertSee('Status kas di bawah terisi <b>otomatis</b>', false);

        // Does NOT contain interactive input checkboxes in table
        $response->assertDontSee('class="checkbox-absensi');

        // Notifications target
        $resNotif = $this->actingAs($siswaUser)->get(route('siswa.notifications.index'));
        $resNotif->assertStatus(200);
        $resNotif->assertSee('id="tour-siswa-notifications-list"', false);
    }

    public function test_wali_kelas_views_contain_tour_trigger_and_targets(): void
    {
        $waliKelasUser = User::where('role', 'wali_kelas')->first();
        $this->assertNotNull($waliKelasUser);

        $resDashboard = $this->actingAs($waliKelasUser)->get(route('wali-kelas.dashboard'));
        $resDashboard->assertStatus(200);
        $resDashboard->assertSee('start-tour');
        $resDashboard->assertSee('Panduan Alur MyCash');
        $resDashboard->assertSee('onboardingTour');
        $resDashboard->assertSee('id="tour-wali-stats"', false);

        $resStudents = $this->actingAs($waliKelasUser)->get(route('wali-kelas.students.index'));
        $resStudents->assertStatus(200);
        $resStudents->assertSee('id="tour-wali-students-table"', false);

        $resReport = $this->actingAs($waliKelasUser)->get(route('wali-kelas.report.index'));
        $resReport->assertStatus(200);
        $resReport->assertSee('id="tour-wali-report-filter"', false);
        $resReport->assertSee('id="tour-wali-report-stats"', false);
        $resReport->assertSee('id="tour-wali-report-chart"', false);
        $resReport->assertSee('id="tour-wali-report-table"', false);
        $resReport->assertSee('id="tour-wali-report-actions"', false);
    }

    public function test_admin_views_contain_tour_trigger_and_targets(): void
    {
        $adminUser = User::where('role', 'admin')->first();
        $this->assertNotNull($adminUser);

        $resDashboard = $this->actingAs($adminUser)->get(route('admin.dashboard'));
        $resDashboard->assertStatus(200);
        $resDashboard->assertSee('start-tour');
        $resDashboard->assertSee('Panduan Alur MyCash');
        $resDashboard->assertSee('onboardingTour');
        $resDashboard->assertSee('id="tour-admin-stats"', false);

        $resKelas = $this->actingAs($adminUser)->get(route('admin.kelas.index'));
        $resKelas->assertStatus(200);
        $resKelas->assertSee('id="tour-admin-kelas-table"', false);
    }

    public function test_bendahara_create_transaction_form_has_auto_allocation_callout(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $this->assertNotNull($bendaharaUser);

        $response = $this->actingAs($bendaharaUser)->get(route('bendahara.transactions.create'));
        $response->assertStatus(200);

        $response->assertSee('Catatan:');
        $response->assertSee('Nominal setoran kas ini akan dialokasikan secara otomatis');
    }
}
