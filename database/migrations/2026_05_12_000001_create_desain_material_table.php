<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('desain_material', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('desain_rumah_id');
            $table->unsignedBigInteger('material_id');
            $table->integer('quantity')->default(1);
            $table->string('unit')->default('unit');
            $table->timestamps();

            $table->foreign('desain_rumah_id')->references('id')->on('desain_rumah')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->unique(['desain_rumah_id', 'material_id']);
            $table->index('desain_rumah_id');
            $table->index('material_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desain_material');
    }
};
