<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            if (!Schema::hasColumn('kelas', 'tahun_ajaran')) {
                $table->string('tahun_ajaran', 15)->default('2025/2026')->after('bulan_selesai');
            }
        });

        Schema::table('transaksi_kas', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi_kas', 'tahun_ajaran')) {
                $table->string('tahun_ajaran', 15)->nullable()->after('jenis_transaksi');
            }
        });

        Schema::table('detail_transaksi_kas', function (Blueprint $table) {
            if (!Schema::hasColumn('detail_transaksi_kas', 'tahun_ajaran')) {
                $table->string('tahun_ajaran', 15)->nullable()->after('periode');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_transaksi_kas', function (Blueprint $table) {
            if (Schema::hasColumn('detail_transaksi_kas', 'tahun_ajaran')) {
                $table->dropColumn('tahun_ajaran');
            }
        });

        Schema::table('transaksi_kas', function (Blueprint $table) {
            if (Schema::hasColumn('transaksi_kas', 'tahun_ajaran')) {
                $table->dropColumn('tahun_ajaran');
            }
        });

        Schema::table('kelas', function (Blueprint $table) {
            if (Schema::hasColumn('kelas', 'tahun_ajaran')) {
                $table->dropColumn('tahun_ajaran');
            }
        });
    }
};
