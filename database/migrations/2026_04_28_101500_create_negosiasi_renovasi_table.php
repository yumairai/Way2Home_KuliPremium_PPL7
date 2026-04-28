<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negosiasi_renovasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_renovasi_id')->constrained('request_renovasi')->cascadeOnDelete();
            $table->foreignId('penawaran_renovasi_id')->nullable()->constrained('penawaran_renovasi')->nullOnDelete();
            $table->enum('pengirim', ['customer', 'mandor']);
            $table->enum('tipe', ['penawaran', 'negosiasi', 'tanggapan', 'tolak', 'setuju'])->default('tanggapan');
            $table->text('pesan');
            $table->bigInteger('nominal_tawaran')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negosiasi_renovasi');
    }
};
