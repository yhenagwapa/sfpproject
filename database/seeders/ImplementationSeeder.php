<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Implementation;

class ImplementationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cycle = Implementation::create([
            'id' => 1,
            'name' => 'Sample Cycle Implementation',
            'school_year_from' => '2025',
            'school_year_to' => '2026',
            'target' => '150000',
            'allocation' => '15000000',
            'status' => 'active',
            'type' => 'regular',
        ]);

    }
}
