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

        // Process pending disability reports and generate PDFs from the data
        $schedule->command('reports:process-pending-disabilities')->everyFiveMinutes();

        // Process pending undernourished upon entry reports and generate PDFs from the data
        $schedule->command('reports:process-pending-undernourished-upon-entry')->everyFiveMinutes();

        // Process pending undernourished after 120 reports and generate PDFs from the data
        $schedule->command('reports:process-pending-undernourished-after-120')->everyFiveMinutes();

        // Process pending nutritional status reports and generate PDFs from the data
        $schedule->command('reports:process-pending-ns-wfa')->everyFiveMinutes();
        $schedule->command('reports:process-pending-ns-hfa')->everyFiveMinutes();
        $schedule->command('reports:process-pending-ns-wfh')->everyFiveMinutes();
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
