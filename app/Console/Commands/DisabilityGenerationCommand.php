<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Reports\DisabilityReportGeneration;

class DisabilityGenerationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:disabilities {user_id} {cdc_id=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generation of persons with disabilities report.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $cdcId = $this->argument('cdc_id');

        try {
            $this->info('Fetching and storing PWD children data...');
            $reportId = DisabilityReportGeneration::generateDisabilityReport($userId, $cdcId);
            $this->info("Data stored successfully. Report ID: {$reportId}");
            $this->info("Status: pending (will be processed by cron job)");

        } catch (\Exception $e) {
            $this->error('Error generating disability report: ' . $e->getMessage());
            throw $e;
        }
    }
}
