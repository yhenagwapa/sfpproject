<?php

namespace App\Console\Commands;

use App\Http\Controllers\Reports\MonitoringReportGeneration;
use Illuminate\Console\Command;

class MonitoringGenerationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:monitoring {user_id} {cdc_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate mnitoring report.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');  // authenticated user ID
        $cdcId  = $this->argument('cdc_id');   // selected CDC

        MonitoringReportGeneration::generateMonitoringReport($userId, $cdcId);

        $this->info('Generating masterlist report.');
    }
}
