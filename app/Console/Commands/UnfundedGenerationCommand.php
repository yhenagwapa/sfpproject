<?php

namespace App\Console\Commands;

use App\Http\Controllers\Reports\UnfundedReportGeneration;
use Illuminate\Console\Command;

class UnfundedGenerationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:unfunded {user_id} {cdc_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generation of unfunded report.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');  // authenticated user ID
        $cdcId  = $this->argument('cdc_id');   // selected CDC

        UnfundedReportGeneration::generateUnfundedReport($userId, $cdcId);

        $this->info('Generating masterlist report.');
    }
}
