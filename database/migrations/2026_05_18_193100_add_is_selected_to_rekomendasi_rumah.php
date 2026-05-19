<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rekomendasi_rumah', function (Blueprint $table) {
            if (!Schema::hasColumn('rekomendasi_rumah', 'is_selected')) {
                $table->boolean('is_selected')->default(false)->after('skor_rekomendasi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rekomendasi_rumah', function (Blueprint $table) {
            $table->dropColumn('is_selected');
        });
    }
};
