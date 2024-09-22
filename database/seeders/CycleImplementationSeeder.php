<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CycleImplementation;

class CycleImplementationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cycle = CycleImplementation::create([
            'cycle_name' => '14th Cycle Implementation',
            'cycle_school_year' => '2024-2025',
            'cycle_target' => '20000',
            'cycle_allocation' => '200000',
            'cycle_status' => 'active',
            'created_by_user_id' => '1',
            'updated_by_user_id' => '1',
        ]);
    }
}
