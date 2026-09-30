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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kelas')->unique();
            $table->string('nama_kelas');
            $table->foreignId('id_wali_kelas')->nullable()->constrained('wali_kelas')->onDelete('set null');
            $table->enum('tipe_periode', ['mingguan', 'bulanan'])->default('bulanan');
            $table->decimal('nominal_standar', 15, 2)->default(20000.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
