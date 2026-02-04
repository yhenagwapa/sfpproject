<?php

namespace App\Console\Commands;

use App\Http\Controllers\Reports\NutritionalStatusWFHReportGeneration;
use Illuminate\Console\Command;

class NsWeightForHeightCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:ns-wfh {user_id} {cdc_id=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Nutritional Status Weight-for-Height report.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $cdcId  = $this->argument('cdc_id');

        try {
            $this->info('Fetching and storing NS Weight-for-Height data...');
            $reportId = NutritionalStatusWFHReportGeneration::generate($userId, $cdcId);
            $this->info("Data stored successfully. Report ID: {$reportId}");
            $this->info("Status: pending (will be processed by cron job)");

        } catch (\Exception $e) {
            $this->error('Error generating NS Weight-for-Height report: ' . $e->getMessage());
            throw $e;
        }
    }
}
