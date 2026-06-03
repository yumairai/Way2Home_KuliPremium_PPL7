<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rumah_dataset', function (Blueprint $table) {
            $table->id();
            $table->string('nama_rumah');
            $table->string('lokasi');
            $table->integer('luas_tanah');
            $table->integer('jumlah_kamar');
            $table->integer('jumlah_lantai');
            $table->integer('tahun_bangun');
            $table->bigInteger('harga');
            $table->text('material_digunakan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rumah_dataset');
    }
};
