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
        Schema::create('desain_rumahs', function (Blueprint $table) 
        {
            $table->id();
            $table->string('tipe_rumah');
            $table->text('deskripsi');
            $table->integer('luas_tanah');
            $table->integer('luas_bangunan');
            $table->integer('jumlah_kamar_tidur');
            $table->integer('jumlah_kamar_mandi');
            $table->bigInteger('estimasi_biaya');
            $table->integer('estimasi_durasi');
            $table->string('material_utama');
            $table->string('path_gambar_desain')->nullable();
            $table->text('fasilitas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desain_rumahs');
    }
};
