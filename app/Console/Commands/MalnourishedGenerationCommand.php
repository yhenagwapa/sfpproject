<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Reports\MalnourishedReportGeneration;

class MalnourishedGenerationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:malnourished {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generation of malnourished report.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');  // authenticated user ID

        try {
            // Generate and store report data
            // Status will be set to 'pending' for cron job to process
            $this->info('Fetching and storing malnourished children data...');
            $reportId = MalnourishedReportGeneration::generateMalnourishedReport($userId);
            $this->info("Data stored successfully. Report ID: {$reportId}");
            $this->info("Status: pending (will be processed by cron job)");

        } catch (\Exception $e) {
            $this->error('Error generating malnourished report: ' . $e->getMessage());
            throw $e;
        }
    }
}
