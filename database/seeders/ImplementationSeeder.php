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
            'name' => 'Sample Cycle Implementation',
            'school_year' => '2024-2025',
            'target' => '20000',
            'allocation' => '200000',
            'status' => 'active',
            'type' => 'regular',
            'created_by_user_id' => '1',
            'updated_by_user_id' => '1',
        ]);

        $milk = Implementation::create([
            'name' => 'Sample Milk Feeding',
            'school_year' => '2024-2025',
            'target' => '10000',
            'allocation' => '100000',
            'status' => 'active',
            'type' => 'milk',
            'created_by_user_id' => '1',
            'updated_by_user_id' => '1',
        ]);
    }
}
