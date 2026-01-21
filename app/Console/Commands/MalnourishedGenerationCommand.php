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

        MalnourishedReportGeneration::generateMalnourishedReport($userId);

        $this->info('Generating malnourished report.');
    }
}
