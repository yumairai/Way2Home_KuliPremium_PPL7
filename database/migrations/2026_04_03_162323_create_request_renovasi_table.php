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
        Schema::create('request_renovasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->text('deskripsi_renovasi');
            $table->bigInteger('budget_estimasi');
            $table->text('path_foto_detail')->nullable();
            $table->text('alamat');
            $table->enum('status_request', ['pending', 'disetujui', 'ditolak', 'selesai'])->default('pending');
            $table->date('tanggal_request');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_renovasi');
    }
};
