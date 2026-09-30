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
        Schema::create('log_transaksi_kas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_transaksi_kas')->nullable()->index();
            $table->string('kode_kelas', 20);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('aksi', ['create', 'edit', 'delete'])->default('edit');
            $table->string('alasan')->nullable();
            $table->json('data_sebelumnya')->nullable();
            $table->json('data_sesudahnya')->nullable();
            $table->timestamps();

            $table->foreign('kode_kelas')->references('kode_kelas')->on('kelas')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_transaksi_kas');
    }
};
