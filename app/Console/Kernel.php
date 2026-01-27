<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Process one job from the queue every 5 minutes
        // This handles report generation (generates PDFs for most reports, populates data for malnourished)
        $schedule->command('queue:work --stop-when-empty --max-jobs=1')->everyMinute();

        // Process pending malnourished reports and generate PDFs from the data
        $schedule->command('reports:process-pending-malnourished')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
