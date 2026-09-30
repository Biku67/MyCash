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
        Schema::create('detail_transaksi_kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_transaksi_kas')->constrained('transaksi_kas')->onDelete('cascade');
            $table->foreignId('id_siswa')->nullable()->constrained('siswa')->onDelete('cascade');
            $table->string('periode')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi_kas');
    }
};
