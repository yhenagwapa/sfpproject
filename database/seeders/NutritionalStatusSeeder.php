<?php

namespace Database\Seeders;

use App\Models\Child;
use App\Models\NutritionalStatus;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NutritionalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Child::all()->each(function ($child) {
            NutritionalStatus::factory()
                ->withChild($child) // custom method
                ->create();
        });
    }
}
