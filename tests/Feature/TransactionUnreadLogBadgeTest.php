<?php

namespace Tests\Feature;

use App\Models\Bendahara;
use App\Models\Kelas;
use App\Models\LogTransaksiKas;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionUnreadLogBadgeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_log_count_badge_only_shows_unread_logs_and_disappears_after_viewing(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $this->assertNotNull($bendaharaUser);
        $bendahara = $bendaharaUser->bendahara;
        $this->assertNotNull($bendahara);

        // Ensure no previous logs exist for clean state
        LogTransaksiKas::where('kode_kelas', $bendahara->kode_kelas)->delete();

        // 1. Initial state: 0 unread logs -> badge should NOT appear
        $response = $this->actingAs($bendaharaUser)->get(route('bendahara.transactions.index'));
        $response->assertStatus(200);
        $response->assertViewHas('logsCount', 0);
        $response->assertDontSee('bg-navy text-white rounded-full');

        // 2. Create 2 unread logs for this class
        LogTransaksiKas::create([
            'id_transaksi_kas' => null,
            'user_id' => $bendaharaUser->id,
            'kode_kelas' => $bendahara->kode_kelas,
            'aksi' => 'edit',
            'alasan' => 'Salah nominal',
            'data_sebelumnya' => ['total_nominal' => 20000],
            'data_sesudahnya' => ['total_nominal' => 25000],
            'is_read' => false,
        ]);

        LogTransaksiKas::create([
            'id_transaksi_kas' => null,
            'user_id' => $bendaharaUser->id,
            'kode_kelas' => $bendahara->kode_kelas,
            'aksi' => 'delete',
            'alasan' => 'Dibatalkan',
            'data_sebelumnya' => ['total_nominal' => 50000],
            'data_sesudahnya' => null,
            'is_read' => false,
        ]);

        // Visit index -> badge should show count 2
        $response = $this->actingAs($bendaharaUser)->get(route('bendahara.transactions.index'));
        $response->assertStatus(200);
        $response->assertViewHas('logsCount', 2);
        $response->assertSee('bg-navy text-white rounded-full');
        $response->assertSee('2');

        // 3. Visit logs page -> should mark all unread logs for this class as is_read = true
        $logsResponse = $this->actingAs($bendaharaUser)->get(route('bendahara.transactions.logs'));
        $logsResponse->assertStatus(200);

        // Assert logs in database are marked as read
        $unreadCount = LogTransaksiKas::where('kode_kelas', $bendahara->kode_kelas)
            ->where('is_read', false)
            ->count();
        $this->assertEquals(0, $unreadCount);

        $readCount = LogTransaksiKas::where('kode_kelas', $bendahara->kode_kelas)
            ->where('is_read', true)
            ->count();
        $this->assertEquals(2, $readCount);

        // 4. Return to transactions index -> logsCount is 0, badge disappears
        $responseAfterRead = $this->actingAs($bendaharaUser)->get(route('bendahara.transactions.index'));
        $responseAfterRead->assertStatus(200);
        $responseAfterRead->assertViewHas('logsCount', 0);
        $responseAfterRead->assertDontSee('bg-navy text-white rounded-full');
    }

    public function test_logs_marked_as_read_only_affect_the_authenticated_bendahara_class(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $bendahara = $bendaharaUser->bendahara;

        // Create a different class and log
        $otherKelas = Kelas::firstOrCreate(
            ['kode_kelas' => 'XII-RPL-2'],
            ['nama_kelas' => 'XII RPL 2', 'nominal_standar' => 20000, 'tipe_periode' => 'bulanan']
        );

        LogTransaksiKas::create([
            'id_transaksi_kas' => null,
            'user_id' => $bendaharaUser->id,
            'kode_kelas' => $otherKelas->kode_kelas,
            'aksi' => 'delete',
            'alasan' => 'Log kelas lain',
            'is_read' => false,
        ]);

        // Current bendahara visits their logs page
        $this->actingAs($bendaharaUser)->get(route('bendahara.transactions.logs'));

        // The other class's log must still be unread (is_read = false)
        $otherClassUnread = LogTransaksiKas::where('kode_kelas', $otherKelas->kode_kelas)
            ->where('is_read', false)
            ->count();
        $this->assertEquals(1, $otherClassUnread);
    }
}
