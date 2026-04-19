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
        Schema::create('pembayaran_proyek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')->constrained('proyek')->cascadeOnDelete();

            $table->string('snap_token')->nullable();
            $table->string('order_id')->nullable();

            $table->bigInteger('jumlah_bayar');
            $table->string('tipe_pembayaran'); // Contoh: 'DP', 'Termin 1'
            $table->enum('status_pembayaran', ['pending', 'berhasil', 'gagal', 'diverifikasi'])->default('pending');
            $table->date('tanggal_pembayaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_proyek');
    }
};
