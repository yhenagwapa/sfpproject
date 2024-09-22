<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sex;

class SexSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $male = Sex::create([
            'name' => 'Male'
        ]);
        $female = Sex::create([
            'name' => 'Female'
        ]);
    }
}
