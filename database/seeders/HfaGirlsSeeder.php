<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class HfaGirlsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use base_path() to point to the correct CSV location in the project root
        $csv = Reader::createFromPath(public_path('/dataseeders/cgshfagirls.csv'), 'r');
        $csv->setHeaderOffset(0); // Set the CSV header row as key

        foreach ($csv as $record) {
            DB::table('cgs_hfa_girls')->insert([
                'age_month' => $record['age_month'],
                'severly_stunted' => $record['severly_stunted'],
                'stunted_from' => $record['stunted_from'],
                'stunted_to' => $record['stunted_to'],
                'normal_from' => $record['normal_from'],
                'normal_to' => $record['normal_to'],
                'tall' => $record['tall'],
            ]);
        }
    }
}
