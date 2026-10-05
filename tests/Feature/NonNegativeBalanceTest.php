<?php

namespace Tests\Feature;

use App\Models\Bendahara;
use App\Models\Kategori;
use App\Models\Kelas;
use App\Models\TransaksiKas;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NonNegativeBalanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_bendahara_cannot_record_expense_exceeding_current_saldo(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $bendahara = $bendaharaUser->bendahara;

        // Current saldo is calculated
        $totalIn = (float)TransaksiKas::where('kode_kelas', $bendahara->kode_kelas)->where('jenis_transaksi', 'pemasukan')->sum('total_nominal');
        $totalOut = (float)TransaksiKas::where('kode_kelas', $bendahara->kode_kelas)->where('jenis_transaksi', 'pengeluaran')->sum('total_nominal');
        $currentSaldo = $totalIn - $totalOut;

        $excessiveAmount = $currentSaldo + 1000000;

        $response = $this->actingAs($bendaharaUser)->post(route('bendahara.transactions.store'), [
            'type' => 'expense',
            'amount' => $excessiveAmount,
            'description' => 'Pembelian Barang Sangat Mahal',
            'transaction_date' => now()->format('Y-m-d'),
            'category' => 'Pembelian ATK',
        ]);

        $response->assertSessionHasErrors('amount');
        $this->assertDatabaseMissing('transaksi_kas', [
            'keterangan' => 'Pembelian Barang Sangat Mahal',
            'total_nominal' => $excessiveAmount,
        ]);
    }

    public function test_bendahara_cannot_update_transaction_causing_negative_saldo(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $bendahara = $bendaharaUser->bendahara;

        // Create initial income
        $kategori = Kategori::firstOrCreate(['nama_kategori' => 'Donasi', 'tipe' => 'pemasukan']);
        $incomeTx = TransaksiKas::create([
            'kode_kelas' => $bendahara->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'id_kategori' => $kategori->id,
            'jenis_transaksi' => 'pemasukan',
            'total_nominal' => 50000,
            'tanggal_transaksi' => now()->format('Y-m-d'),
            'keterangan' => 'Uang Donasi Awal',
        ]);

        // Try updating it to expense with huge amount
        $response = $this->actingAs($bendaharaUser)->put(route('bendahara.transactions.update', $incomeTx->id), [
            'type' => 'expense',
            'amount' => 999999999,
            'description' => 'Ganti ke Pengeluaran Besar',
            'transaction_date' => now()->format('Y-m-d'),
            'category' => 'Operasional Kelas',
            'reason' => 'Perubahan anggaran',
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_bendahara_cannot_delete_income_transaction_if_it_causes_negative_saldo(): void
    {
        $bendaharaUser = User::where('role', 'bendahara')->first();
        $bendahara = $bendaharaUser->bendahara;

        $kategoriIn = Kategori::firstOrCreate(['nama_kategori' => 'Donasi Khusus', 'tipe' => 'pemasukan']);
        $incomeTx = TransaksiKas::create([
            'kode_kelas' => $bendahara->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'id_kategori' => $kategoriIn->id,
            'jenis_transaksi' => 'pemasukan',
            'total_nominal' => 100000,
            'tanggal_transaksi' => now()->format('Y-m-d'),
            'keterangan' => 'Donasi Masuk',
        ]);

        $kategoriOut = Kategori::firstOrCreate(['nama_kategori' => 'Operasional Khusus', 'tipe' => 'pengeluaran']);
        $expenseTx = TransaksiKas::create([
            'kode_kelas' => $bendahara->kode_kelas,
            'id_bendahara' => $bendahara->id,
            'id_kategori' => $kategoriOut->id,
            'jenis_transaksi' => 'pengeluaran',
            'total_nominal' => 80000,
            'tanggal_transaksi' => now()->format('Y-m-d'),
            'keterangan' => 'Beli Keperluan',
        ]);

        // Attempt to delete incomeTx when current balance would drop below 0 if deleted
        // (Assuming other transactions don't cover the 80000 expense)
        // If other transactions exist in seeder, delete with an amount that exceeds total other balance
        $otherBalance = (float)TransaksiKas::where('kode_kelas', $bendahara->kode_kelas)->where('jenis_transaksi', 'pemasukan')->sum('total_nominal')
            - (float)TransaksiKas::where('kode_kelas', $bendahara->kode_kelas)->where('jenis_transaksi', 'pengeluaran')->sum('total_nominal');
        
        // Add expense equal to current remaining balance
        if ($otherBalance > 0) {
            TransaksiKas::create([
                'kode_kelas' => $bendahara->kode_kelas,
                'id_bendahara' => $bendahara->id,
                'id_kategori' => $kategoriOut->id,
                'jenis_transaksi' => 'pengeluaran',
                'total_nominal' => $otherBalance,
                'tanggal_transaksi' => now()->format('Y-m-d'),
                'keterangan' => 'Habiskan Saldo',
            ]);
        }

        // Now deleting incomeTx would cause negative balance
        $response = $this->actingAs($bendaharaUser)->delete(route('bendahara.transactions.destroy', $incomeTx->id), [
            'reason' => 'Ingin hapus donasi',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('transaksi_kas', ['id' => $incomeTx->id]);
    }

    public function test_kelas_saldo_kas_accessor_is_never_negative(): void
    {
        $kelas = Kelas::first();
        $this->assertNotNull($kelas);
        $this->assertGreaterThanOrEqual(0, $kelas->saldo_kas);
    }
}

