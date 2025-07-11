<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class RevisedWFHGirlsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use base_path() to point to the correct CSV location in the project root
        $csv = Reader::createFromPath(public_path('/dataseeders/REVISED WFH GIRLS.csv'), 'r');
        $csv->setHeaderOffset(0); // Set the CSV header row as key

        foreach ($csv as $record) {
            DB::table('cgs_wfh_girls')->insert([
                'length_from' => $record['length_from'],
                'length_to' => $record['length_to'],
                'severely_wasted' => $record['severely_wasted'],
                'wasted_from' => $record['wasted_from'],
                'wasted_to' => $record['wasted_to'],
                'normal_from' => $record['normal_from'],
                'normal_to' => $record['normal_to'],
                'overweight_from' => $record['overweight_from'],
                'overweight_to' => $record['overweight_to'],
                'obese' => $record['obese'],
            ]);
        }
    }
}
