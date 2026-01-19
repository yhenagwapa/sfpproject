<?php

namespace App\Console\Commands;

use App\Http\Controllers\Reports\AgeBracketAfter120;
use Illuminate\Console\Command;

class AgeBracketAfter120Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:age-bracket-after-120 {user_id} {cdc_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate age bracket after 120 report.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');  // authenticated user ID
        $cdcId  = $this->argument('cdc_id');   // selected CDC

        AgeBracketAfter120::generateAgeBracketAfter120Report($userId, $cdcId);

        $this->info('Generating report for age bracket after 120 feedings.');
    }
}
