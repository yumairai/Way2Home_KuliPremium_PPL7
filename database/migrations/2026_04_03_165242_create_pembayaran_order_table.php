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
        Schema::create('pembayaran_order', function (Blueprint $table) 
        {
            $table->id();            
            $table->foreignId('order_material_id')
                  ->constrained('order_material')
                  ->cascadeOnDelete();
            
            $table->bigInteger('jumlah_bayar');
            $table->string('metode_pembayaran');
            
            $table->enum('status_pembayaran', ['pending', 'berhasil', 'gagal', 'diverifikasi'])
                  ->default('pending');
                  
            $table->date('tanggal_pembayaran');
            
            $table->string('path_bukti_bayar')->nullable(); 
            $table->string('transaction_id')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_order');
    }
};
