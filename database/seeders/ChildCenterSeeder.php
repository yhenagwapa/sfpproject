<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChildCenter;
use Illuminate\Support\Facades\Hash;

class ChildCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assignment1 = ChildCenter::create([
            'child_id' => '1',
            'child_development_center_id' => '2',
            'implementation_id' => '3',
            'milk_feeding_id' => '4',
            'status' => 'active',
            'funded' => true,
        ]);

        $assignment2 = ChildCenter::create([
            'child_id' => '2',
            'child_development_center_id' => '2',
            'implementation_id' => '3',
            'milk_feeding_id' => '4',
            'status' => 'inactive',
            'funded' => true,
        ]);

        $assignment3 = ChildCenter::create([
            'child_id' => '3',
            'child_development_center_id' => '3',
            'status' => 'active',
            'funded' => false,
        ]);

        $assignment4 = ChildCenter::create([
            'child_id' => '4',
            'child_development_center_id' => '1',
            'implementation_id' => '3',
            'milk_feeding_id' => '4',
            'status' => 'active',
            'funded' => true,
        ]);

        $assignment5 = ChildCenter::create([
            'child_id' => '5',
            'child_development_center_id' => '2',
            'implementation_id' => '3',
            'milk_feeding_id' => '4',
            'status' => 'active',
            'funded' => true,
        ]);

        $assignment6 = ChildCenter::create([
            'child_id' => '6',
            'child_development_center_id' => '3',
            'implementation_id' => '3',
            'milk_feeding_id' => '4',
            'status' => 'active',
            'funded' => true,
        ]);

    }
}
