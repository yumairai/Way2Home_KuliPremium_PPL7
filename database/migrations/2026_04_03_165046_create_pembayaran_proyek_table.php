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
        Schema::create('pembayaran_proyeks', function (Blueprint $table) 
        {
            $table->id();
            $table->foreignId('proyek_id')->constrained('proyeks')->cascadeOnDelete();
            $table->bigInteger('jumlah_bayar');
            $table->string('tipe_pembayaran'); // Contoh: 'DP', 'Termin 1', 'Pelunasan'
            $table->string('metode_pembayaran'); // Contoh: 'Transfer Bank', 'Midtrans', 'E-Wallet'
            $table->enum('status_pembayaran', ['pending', 'berhasil', 'gagal', 'diverifikasi'])->default('pending');
            $table->date('tanggal_pembayaran');
            $table->string('path_bukti_bayar')->nullable(); // Penting untuk verifikasi manual
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_proyeks');
    }
};
