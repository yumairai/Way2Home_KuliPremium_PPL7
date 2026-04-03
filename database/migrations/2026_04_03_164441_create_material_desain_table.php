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
        Schema::create('material_desains', function (Blueprint $table) 
        {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->foreignId('desain_rumah_id')->constrained('desain_rumahs')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->string('satuan')->nullable(); // Contoh: 'sak', 'm3', 'lembar'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_desains');
    }
};
