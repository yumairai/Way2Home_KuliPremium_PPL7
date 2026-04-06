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
        Schema::create('detail_order', function (Blueprint $table) 
        {
            $table->id();
            $table->foreignId('order_material_id')
                  ->constrained('order_material')
                  ->cascadeOnDelete();            
            $table->foreignId('material_id')
                  ->constrained('materials')
                  ->cascadeOnDelete();
            $table->integer('jumlah');
            $table->bigInteger('subtotal');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_order');
    }
};
