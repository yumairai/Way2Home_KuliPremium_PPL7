<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('request_renovasi') && DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE request_renovasi MODIFY path_foto_detail TEXT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('request_renovasi') && DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE request_renovasi MODIFY path_foto_detail VARCHAR(255) NULL');
        }
    }
};
