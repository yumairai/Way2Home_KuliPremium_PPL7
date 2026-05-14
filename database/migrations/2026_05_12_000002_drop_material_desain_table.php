<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove legacy material_desain table
     * Replaced by desain_material (2026_05_12_000001)
     */
    public function up(): void
    {
        if (Schema::hasTable('material_desain')) {
            Schema::dropIfExists('material_desain');
        }
    }

    public function down(): void
    {
        Schema::create('material_desain', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->foreignId('desain_rumah_id')->constrained('desain_rumah')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->string('satuan')->nullable();
            $table->timestamps();
        });
    }
};
