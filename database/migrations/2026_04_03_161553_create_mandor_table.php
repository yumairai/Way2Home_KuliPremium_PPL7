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
        Schema::create('mandors', function (Blueprint $table) 
        {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('sertifikasi')->nullable();
            $table->string('path_foto_profil')->nullable();
            $table->string('area_kerja');
            $table->string('path_foto_ktp')->nullable();
            $table->integer('lama_pengalaman');
            $table->enum('status', ['aktif', 'nonaktif', 'suspend'])->default('aktif');
            $table->float('rating')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mandors');
    }
};
