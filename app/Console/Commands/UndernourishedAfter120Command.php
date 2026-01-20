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
    protected $signature = 'reports:undernourished-after120 {user_id} {cdc_id}';

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

        UndernourishedAfter120ReportGeneration::generateUndernourishedAfter120Report($userId, $cdcId);

        $this->info('Generating report for undernourished children after 120 feedings.');
    }
}
