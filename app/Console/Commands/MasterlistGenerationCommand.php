<?php

namespace App\Console\Commands;

use App\Http\Controllers\Reports\MasterlistReportGeneration;
use Illuminate\Console\Command;


class MasterlistGenerationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:masterlist {user_id} {cdc_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate masterlist report.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');  // authenticated user ID
        $cdcId  = $this->argument('cdc_id');   // selected CDC

        MasterlistReportGeneration::generateMasterlistReport($userId, $cdcId);

        $this->info('Generating masterlist report.');
    }
}
