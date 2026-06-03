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
 
            // 0 = DP, 1-3 = cicilan
            $table->unsignedTinyInteger('periode')->default(0);
 
            $table->bigInteger('jumlah_bayar');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->date('tanggal_bayar')->nullable();
 
            // Midtrans
            $table->string('snap_token')->nullable();
            $table->string('order_id')->nullable();
            $table->string('metode_pembayaran')->nullable();
 
            $table->enum('status_pembayaran', [
                'belum_bayar',
                'pending',
                'berhasil',
                'gagal',
                'jatuh_tempo',
            ])->default('belum_bayar');
 
            $table->timestamps();
 
            $table->unique(['proyek_id', 'periode']);
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
