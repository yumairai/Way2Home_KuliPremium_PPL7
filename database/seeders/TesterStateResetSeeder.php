<?php

namespace Database\Seeders;

use App\Http\Middleware\BypassTesterRequest;
use Illuminate\Database\Seeder;

class TesterStateResetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BypassTesterRequest::resetTesterState();
    }
}
