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
        Schema::table('carts', function (Blueprint $table) {
            // Index untuk query filtering user cart
            $table->index(['user_id', 'material_id']);

            // Separate index untuk user_id lookups
            $table->index('user_id');

            // Separate index untuk material_id lookups
            $table->index('material_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'material_id']);
            $table->dropIndex('user_id');
            $table->dropIndex('material_id');
        });
    }
};
