<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah index pada kolom yang sering digunakan sebagai filter
     * di bagian Mandor. Tidak mengubah nama kolom, tipe data, atau logika bisnis.
     */
    public function up(): void
    {
        // Index pada tabel proyek untuk filter mandor
        Schema::table('proyek', function (Blueprint $table) {
            $table->index('mandor_id',    'idx_proyek_mandor_id');
            $table->index('status_proyek','idx_proyek_status_proyek');
            $table->index('jenis_proyek', 'idx_proyek_jenis_proyek');
        });

        // Index pada tabel request_renovasi untuk filter status
        Schema::table('request_renovasi', function (Blueprint $table) {
            $table->index('status_request', 'idx_request_renovasi_status');
        });

        // Index pada tabel penawaran_renovasi untuk filter mandor + status
        Schema::table('penawaran_renovasi', function (Blueprint $table) {
            $table->index('mandor_id',        'idx_penawaran_mandor_id');
            $table->index('status_penawaran', 'idx_penawaran_status');
        });

        // Index pada tabel mandor_activity_histories untuk filter mandor
        Schema::table('mandor_activity_histories', function (Blueprint $table) {
            $table->index('mandor_id', 'idx_activity_history_mandor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyek', function (Blueprint $table) {
            $table->dropIndex('idx_proyek_mandor_id');
            $table->dropIndex('idx_proyek_status_proyek');
            $table->dropIndex('idx_proyek_jenis_proyek');
        });

        Schema::table('request_renovasi', function (Blueprint $table) {
            $table->dropIndex('idx_request_renovasi_status');
        });

        Schema::table('penawaran_renovasi', function (Blueprint $table) {
            $table->dropIndex('idx_penawaran_mandor_id');
            $table->dropIndex('idx_penawaran_status');
        });

        Schema::table('mandor_activity_histories', function (Blueprint $table) {
            $table->dropIndex('idx_activity_history_mandor_id');
        });
    }
};
