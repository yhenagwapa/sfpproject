<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class Wfa6AboveGirlsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use base_path() to point to the correct CSV location in the project root
        $csv = Reader::createFromPath(public_path('/dataseeders/weight_for_age_6years_above_girls.csv'), 'r');
        $csv->setHeaderOffset(0); // Set the CSV header row as key

        foreach ($csv as $record) {
            DB::table('cgs_wfa_girls')->insert([
                'age_month' => $record['age_in_months'],
                'severly_underweight' => $record['severely_underweight'],
                'underweight_from' => $record['underweight_from'],
                'underweight_to' => $record['underweight_to'],
                'normal_from' => $record['normal_from'],
                'normal_to' => $record['normal_to'],
                'overweight' => $record['overweight'],
            ]);
        }
    }
}
