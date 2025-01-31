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
            'psgc_id' => '211',
        ]);

        $center2 = ChildDevelopmentCenter::create([
            'center_name' => 'CDC 2',
            'address' => '123 abc !@#',
            'psgc_id' => '536',
        ]);

        $center3 = ChildDevelopmentCenter::create([
            'center_name' => 'CDC 3',
            'address' => '123 abc !@#',
            'psgc_id' => '540',
        ]);
    }
}
