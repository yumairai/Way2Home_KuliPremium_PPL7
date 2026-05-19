<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_images_global', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // Minimalist, Modern, Mewah, Premium
            $table->integer('urutan')->default(1); // 1-3 (3 gambar per kategori)
            $table->string('path_gambar'); // URL/path to image
            $table->string('deskripsi')->nullable();
            $table->timestamps();

            // Unique: only 1 image per kategori+urutan
            $table->unique(['kategori', 'urutan']);
            $table->index('kategori');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('design_images_global');
    }
};
