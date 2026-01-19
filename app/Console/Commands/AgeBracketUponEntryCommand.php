<?php

namespace App\Console\Commands;

use App\Models\AgeBracketUponEntry;
use Illuminate\Console\Command;

class AgeBracketUponEntryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:age-bracket-upon-entry {user_id} {cdc_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Age Bracket Upon Entry Report.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');  // authenticated user ID
        $cdcId  = $this->argument('cdc_id');   // selected CDC

        AgeBracketUponEntry::generateAgeBracketUponEntryReport($userId, $cdcId);

        $this->info('Generating age bracket upon entry report.');
    }
}
