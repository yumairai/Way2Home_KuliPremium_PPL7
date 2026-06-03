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
        Schema::create('order_material', function (Blueprint $table) 
        {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            
            $table->string('order_id_midtrans')->unique();
            $table->string('snap_token')->nullable();
            
            $table->date('tanggal_order');
            $table->text('alamat_pengiriman');
            
            $table->bigInteger('subtotal_material');
            $table->bigInteger('biaya_layanan');
            $table->bigInteger('total_harga');
            
            $table->enum('status_order', ['pending','paid','persiapan','dikirim','selesai','expire','cancel','deny'])
                ->default('pending');
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_material');
    }
};