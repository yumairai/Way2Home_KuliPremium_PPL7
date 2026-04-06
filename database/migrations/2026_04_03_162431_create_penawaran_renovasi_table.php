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
        Schema::create('penawaran_renovasi', function (Blueprint $table) 
        {
            $table->id();
            $table->foreignId('request_renovasi_id')->constrained('request_renovasi')->cascadeOnDelete();
            $table->foreignId('mandor_id')->constrained('mandors')->cascadeOnDelete();
            $table->text('analisis_dari_mandor');
            $table->bigInteger('estimasi_biaya');
            $table->integer('estimasi_durasi'); // Misal dalam satuan hari
            $table->enum('status_penawaran', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penawaran_renovasi');
    }
};
