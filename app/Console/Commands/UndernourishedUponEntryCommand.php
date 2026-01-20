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
    protected $signature = 'reports:undernourished-upon-entry {user_id} {cdc_id}';

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
        $userId = $this->argument('user_id');  // authenticated user ID
        $cdcId  = $this->argument('cdc_id');   // selected CDC

        UndernourishedUponEntryReportGeneration::generateUndernourishedUponEntryReport($userId, $cdcId);

        $this->info('Generating masterlist report.');
    }
}
