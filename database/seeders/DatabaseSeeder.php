<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Child;
use App\Models\NutritionalStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            PsgcSeeder::class,
            // ImplementationSeeder::class, for load testing only
            SexSeeder::class,
            AdminSeeder::class,
            HfaBoysSeeder::class,
            HfaGirlsSeeder::class,
            WfaBoysSeeder::class,
            WfaGirlsSeeder::class,
            RevisedWFHBoysSeeder::class,
            RevisedWFHGirlsSeeder::class,
        ]);

        // for load testing
        // User::factory()->count(10)->create(); // 1000
        // Child::factory()->count(100)->create(); // 149,500
        // NutritionalStatus::factory()->count(100)->create(); // 149,500

        // $this->call([
        //     NutritionalStatusSeeder::class,
        // ]);
    }
}
