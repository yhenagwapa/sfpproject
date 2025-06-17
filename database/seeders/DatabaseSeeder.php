<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Child;
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
            // PermissionSeeder::class,
            PsgcSeeder::class,
            // UserSeeder::class,
            // ImplementationSeeder::class,
            // CenterSeeder::class,
            SexSeeder::class,
            // ChildSeeder::class,
            // UserCenterSeeder::class,
            // ChildCenterSeeder::class,
            // HfaBoysSeeder::class,
            // HfaGirlsSeeder::class,
            // WfaBoysSeeder::class,
            // WfaGirlsSeeder::class,
            // WfhBoysSeeder::class,
            // WfhGirlsSeeder::class,
        ]);

        // for loadtesting
        User::factory()->count(10)->create();
        Child::factory()->count(100)->create();
    }
}
