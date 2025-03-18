<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserCenter;
use Illuminate\Support\Facades\Hash;

class UserCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assignment1 = UserCenter::create([
            'user_id' => '2',
            'child_development_center_id' => '1',
        ]);

        $assignment2 = UserCenter::create([
            'user_id' => '4',
            'child_development_center_id' => '2',
        ]);

        $assignment3 = UserCenter::create([
            'user_id' => '2',
            'child_development_center_id' => '3',
        ]);

        $assignment4 = UserCenter::create([
            'user_id' => '3',
            'child_development_center_id' => '1',
        ]);

        $assignment6 = UserCenter::create([
            'user_id' => '3',
            'child_development_center_id' => '3',
        ]);

        $assignment7 = UserCenter::create([
            'user_id' => '5',
            'child_development_center_id' => '2',
        ]);

        $assignment8 = UserCenter::create([
            'user_id' => '6',
            'child_development_center_id' => '2',
        ]);

        $assignment9 = UserCenter::create([
            'user_id' => '7',
            'child_development_center_id' => '1',
        ]);

        $assignment10 = UserCenter::create([
            'user_id' => '7',
            'child_development_center_id' => '3',
        ]);

        $assignment11 = UserCenter::create([
            'user_id' => '8',
            'child_development_center_id' => '1',
        ]);

        $assignment12 = UserCenter::create([
            'user_id' => '8',
            'child_development_center_id' => '2',
        ]);

        $assignment13 = UserCenter::create([
            'user_id' => '8',
            'child_development_center_id' => '3',
        ]);

    }
}
