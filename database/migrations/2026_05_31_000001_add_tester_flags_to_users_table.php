<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add `is_tester` flag used by BypassTesterRequest middleware
     * and `is_first_login` flag for first-login UX flow.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Guard: hanya tambahkan kolom jika belum ada (idempotent migration)
            if (! Schema::hasColumn('users', 'is_tester')) {
                $table->boolean('is_tester')->default(false)->after('avatar');
            }
            if (! Schema::hasColumn('users', 'is_first_login')) {
                $table->boolean('is_first_login')->default(true)->after('is_tester');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_tester', 'is_first_login']);
        });
    }
};
