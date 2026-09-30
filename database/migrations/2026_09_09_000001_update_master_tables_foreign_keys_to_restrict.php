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
        // 1. detail_transaksi_kas.id_siswa -> restrict
        Schema::table('detail_transaksi_kas', function (Blueprint $table) {
            $table->dropForeign(['id_siswa']);
            $table->foreign('id_siswa')->references('id')->on('siswa')->onDelete('restrict');
        });

        // 2. transaksi_kas.id_bendahara -> restrict
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->dropForeign(['id_bendahara']);
            $table->foreign('id_bendahara')->references('id')->on('bendahara')->onDelete('restrict');
        });

        // 3. transaksi_kas.kode_kelas -> restrict
        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->dropForeign(['kode_kelas']);
            $table->foreign('kode_kelas')->references('kode_kelas')->on('kelas')->onDelete('restrict');
        });

        // 4. siswa.kode_kelas -> restrict
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['kode_kelas']);
            $table->foreign('kode_kelas')->references('kode_kelas')->on('kelas')->onDelete('restrict');
        });

        // 5. kelas.id_wali_kelas -> restrict
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropForeign(['id_wali_kelas']);
            $table->foreign('id_wali_kelas')->references('id')->on('wali_kelas')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropForeign(['id_wali_kelas']);
            $table->foreign('id_wali_kelas')->references('id')->on('wali_kelas')->onDelete('set null');
        });

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['kode_kelas']);
            $table->foreign('kode_kelas')->references('kode_kelas')->on('kelas')->onDelete('cascade');
        });

        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->dropForeign(['kode_kelas']);
            $table->foreign('kode_kelas')->references('kode_kelas')->on('kelas')->onDelete('cascade');
        });

        Schema::table('transaksi_kas', function (Blueprint $table) {
            $table->dropForeign(['id_bendahara']);
            $table->foreign('id_bendahara')->references('id')->on('bendahara')->onDelete('cascade');
        });

        Schema::table('detail_transaksi_kas', function (Blueprint $table) {
            $table->dropForeign(['id_siswa']);
            $table->foreign('id_siswa')->references('id')->on('siswa')->onDelete('cascade');
        });
    }
};
