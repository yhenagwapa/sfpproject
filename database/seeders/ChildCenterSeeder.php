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
            'implementation_id' => '1',
        ]);

        $assignment2 = ChildCenter::create([
            'child_id' => '2',
            'child_development_center_id' => '2',
            'implementation_id' => '1',
        ]);

        $assignment3 = ChildCenter::create([
            'child_id' => '3',
            'child_development_center_id' => '3',
            'implementation_id' => '1',
        ]);

        $assignment4 = ChildCenter::create([
            'child_id' => '4',
            'child_development_center_id' => '1',
        ]);

        $assignment5 = ChildCenter::create([
            'child_id' => '5',
            'child_development_center_id' => '2',
            'implementation_id' => '1',
        ]);

        $assignment6 = ChildCenter::create([
            'child_id' => '6',
            'child_development_center_id' => '3',
            'implementation_id' => '1',
        ]);

    }
}
