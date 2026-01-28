<?php

namespace App\Console\Commands;

use App\Http\Controllers\Reports\UndernourishedUponEntryReportGeneration;
use Illuminate\Console\Command;

class UndernourishedUponEntryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:undernourished-upon-entry {user_id} {cdc_id=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generation of undernourished children upon entry report.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $cdcId = $this->argument('cdc_id');

        try {
            $this->info('Fetching and storing undernourished upon entry data...');
            $reportId = UndernourishedUponEntryReportGeneration::generateReport($userId, $cdcId);
            $this->info("Data stored successfully. Report ID: {$reportId}");
            $this->info("Status: pending (will be processed by cron job)");

        } catch (\Exception $e) {
            $this->error('Error generating undernourished upon entry report: ' . $e->getMessage());
            throw $e;
        }
    }
}
