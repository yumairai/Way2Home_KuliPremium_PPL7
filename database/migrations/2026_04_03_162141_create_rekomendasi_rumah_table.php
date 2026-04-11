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
        Schema::create('rekomendasi_rumah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preferensi_rumah_id')->constrained('preferensi_rumah')->cascadeOnDelete();
            $table->foreignId('desain_rumah_id')->constrained('desain_rumah')->cascadeOnDelete();
            $table->float('skor_rekomendasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekomendasi_rumah');
    }
};
