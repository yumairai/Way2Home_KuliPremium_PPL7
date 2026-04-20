<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('desain_rumah', function (Blueprint $table) {
            if (!Schema::hasColumn('desain_rumah', 'lokasi')) {
                $table->string('lokasi')->nullable()->after('deskripsi');
            }

            if (!Schema::hasColumn('desain_rumah', 'gaya_arsitektur')) {
                $table->string('gaya_arsitektur')->nullable()->after('lokasi');
            }

            if (!Schema::hasColumn('desain_rumah', 'jumlah_lantai')) {
                $table->integer('jumlah_lantai')->nullable()->after('jumlah_kamar_mandi');
            }

            if (!Schema::hasColumn('desain_rumah', 'tahun_bangun')) {
                $table->integer('tahun_bangun')->nullable()->after('jumlah_lantai');
            }

            if (!Schema::hasColumn('desain_rumah', 'material_digunakan')) {
                $table->text('material_digunakan')->nullable()->after('material_utama');
            }
        });
    }

    public function down(): void
    {
        Schema::table('desain_rumah', function (Blueprint $table) {
            $dropColumns = [];

            foreach (['lokasi', 'gaya_arsitektur', 'jumlah_lantai', 'tahun_bangun', 'material_digunakan'] as $column) {
                if (Schema::hasColumn('desain_rumah', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
