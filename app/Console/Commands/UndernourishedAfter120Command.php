<?php

namespace App\Console\Commands;

use App\Http\Controllers\Reports\UndernourishedAfter120ReportGeneration;
use Illuminate\Console\Command;

class UndernourishedAfter120Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:undernourished-after-120 {user_id} {cdc_id=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generation of undernourished children after 120 feedings report.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $cdcId = $this->argument('cdc_id');

        try {
            $this->info('Fetching and storing undernourished after 120 data...');
            $reportId = UndernourishedAfter120ReportGeneration::generateReport($userId, $cdcId);
            $this->info("Data stored successfully. Report ID: {$reportId}");
            $this->info("Status: pending (will be processed by cron job)");

        } catch (\Exception $e) {
            $this->error('Error generating undernourished after 120 report: ' . $e->getMessage());
            throw $e;
        }
    }
}
