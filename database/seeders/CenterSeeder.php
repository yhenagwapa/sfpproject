<?php

namespace Database\Seeders;

use App\Models\ChildDevelopmentCenter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $center1 = ChildDevelopmentCenter::create([
            'center_name' => 'CDC 1',
            'address' => '123 abc !@#',
            'psgc_id' => '214',
            'zip_code' => '8000',
            'assigned_user_id' => '1',
            'created_by_user_id' => '3',
        ]);

        $center2 = ChildDevelopmentCenter::create([
            'center_name' => 'CDC 2',
            'address' => '123 abc !@#',
            'psgc_id' => '536',
            'zip_code' => '8000',
            'assigned_user_id' => '2',
            'created_by_user_id' => '3',
        ]);
    }
}
