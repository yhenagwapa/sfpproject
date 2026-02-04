<?php

namespace App\Console\Commands;

use App\Http\Controllers\Reports\NutritionalStatusWFAReportGeneration;
use Illuminate\Console\Command;

class NsWeightForAgeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:ns-wfa {user_id} {cdc_id=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Nutritional Status Weight-for-Age report.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $cdcId  = $this->argument('cdc_id');

        try {
            $this->info('Fetching and storing NS Weight-for-Age data...');
            $reportId = NutritionalStatusWFAReportGeneration::generate($userId, $cdcId);
            $this->info("Data stored successfully. Report ID: {$reportId}");
            $this->info("Status: pending (will be processed by cron job)");

        } catch (\Exception $e) {
            $this->error('Error generating NS Weight-for-Age report: ' . $e->getMessage());
            throw $e;
        }
    }
}
