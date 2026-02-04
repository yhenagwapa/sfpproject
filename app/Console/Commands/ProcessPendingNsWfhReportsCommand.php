<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Reports\NutritionalStatusWFHReportGeneration;
use Illuminate\Support\Facades\DB;

class ProcessPendingNsWfhReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:process-pending-ns-wfh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all pending NS Weight-for-Height reports and generate PDFs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingReports = DB::table('ns_wfh_reports')
            ->where('status', 'pending')
            ->get();

        if ($pendingReports->isEmpty()) {
            $this->info('No pending reports to process.');
            return 0;
        }

        $this->info("Found {$pendingReports->count()} pending report(s) to process.");

        $successCount = 0;
        $failCount = 0;

        foreach ($pendingReports as $report) {
            try {
                $this->info("Processing report ID: {$report->id} for user ID: {$report->user_id}");

                $filePath = NutritionalStatusWFHReportGeneration::generatePDF($report->user_id);

                $this->info("Successfully generated PDF: {$filePath}");
                $successCount++;

            } catch (\Exception $e) {
                $this->error("Failed to process report ID {$report->id}: " . $e->getMessage());
                $failCount++;
            }
        }

        $this->info("\nProcessing complete:");
        $this->info("  Success: {$successCount}");
        $this->info("  Failed: {$failCount}");

        return 0;
    }
}
