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
            $table->string('bulan_mulai', 10)->default('Jan')->nullable()->after('tipe_periode');
            $table->string('bulan_selesai', 10)->default('Des')->nullable()->after('bulan_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn(['bulan_mulai', 'bulan_selesai']);
        });
    }
};
