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
        Schema::create('progress_proyek', function (Blueprint $table) 
        {
            $table->id();
            $table->foreignId('proyek_id')->constrained('proyek')->cascadeOnDelete();
            $table->string('tahap_proyek'); // Contoh: Pondasi, Dinding, Atap
            $table->integer('persentase'); // 0-100%
            $table->text('catatan')->nullable();
            $table->string('path_foto_progress')->nullable();
            $table->date('tanggal_update');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_proyek');
    }
};
