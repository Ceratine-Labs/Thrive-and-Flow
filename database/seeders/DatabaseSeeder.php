<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            ProgramSeeder::class,
        ]);

        // Demo accounts & activity — disable with SEED_DEMO=false once real
        // members exist.
        if (env('SEED_DEMO', true)) {
            $this->call(DemoSeeder::class);
        }
    }
}
