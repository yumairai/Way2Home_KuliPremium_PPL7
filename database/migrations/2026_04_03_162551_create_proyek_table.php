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
        Schema::create('proyek', function (Blueprint $table) 
        {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('mandor_id')->nullable()->constrained('mandors')->nullOnDelete();
            $table->string('jenis_proyek');
            $table->text('alamat_proyek');
            $table->date('tanggal_mulai');
            $table->enum('status_proyek', ['perencanaan', 'berjalan', 'selesai', 'dibatalkan'])->default('perencanaan');
            $table->integer('jumlah_cicilan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek');
    }
};
