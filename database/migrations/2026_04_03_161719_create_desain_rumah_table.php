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
        Schema::create('desain_rumah', function (Blueprint $table) 
        {
            $table->id();
            $table->string('tipe_rumah');
            $table->text('deskripsi');
            
            // Kolom baru dari file "add" sebelumnya
            $table->string('lokasi')->nullable();
            $table->string('gaya_arsitektur')->nullable();
            
            $table->integer('luas_tanah');
            $table->integer('luas_bangunan');
            $table->integer('jumlah_kamar_tidur');
            $table->integer('jumlah_kamar_mandi');
            
            // Kolom baru jumlah_lantai
            $table->integer('jumlah_lantai')->nullable();
            
            $table->bigInteger('estimasi_biaya');
            $table->integer('estimasi_durasi');
            
            // Kolom baru tahun_bangun
            $table->integer('tahun_bangun')->nullable();
            
            $table->string('material_utama');
            
            // Kolom baru material_digunakan (tipe text)
            $table->text('material_digunakan')->nullable();
            
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
        Schema::dropIfExists('desain_rumah');
    }
};