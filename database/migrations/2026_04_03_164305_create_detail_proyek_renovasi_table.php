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
        Schema::create('detail_proyek_renovasi', function (Blueprint $table) 
        {
            $table->id();
            $table->foreignId('proyek_id')->constrained('proyek')->cascadeOnDelete();
            $table->foreignId('request_renovasi_id')->constrained('request_renovasi')->restrictOnDelete();
            $table->foreignId('penawaran_renovasi_id')->constrained('penawaran_renovasi')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_proyek_renovasi');
    }
};
