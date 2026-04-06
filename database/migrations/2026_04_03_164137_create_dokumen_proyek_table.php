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
        Schema::create('dokumen_proyek', function (Blueprint $table) 
        {
            $table->id();            
            $table->foreignId('detail_bangun_id')
                  ->constrained('detail_proyek_bangun')
                  ->cascadeOnDelete();
            $table->string('jenis_dokumen');
            $table->string('file_path');            
            $table->enum('status_verifikasi', ['pending', 'disetujui', 'ditolak'])
                  ->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_proyek');
    }
};
