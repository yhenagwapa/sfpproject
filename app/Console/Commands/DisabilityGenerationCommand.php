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
    protected $signature = 'reports:disabilities {user_id}';

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
        $userId = $this->argument('user_id');  // authenticated user ID
        $cdcId  = $this->argument('cdc_id');   // selected CDC

        DisabilityReportGeneration::generateDisabilityReport($userId);

        $this->info('Generating masterlist report.');
    }
}
