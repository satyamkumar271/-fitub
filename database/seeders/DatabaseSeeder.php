<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(UserDataMigrationSeeder::class);

        // Optional demo data seeding (local testing only)
        // Run with: DEMO_SEED=1 php artisan db:seed
        if (env('DEMO_SEED')) {
            $this->call(FitubDemoSeeder::class);
        }
    }
}
