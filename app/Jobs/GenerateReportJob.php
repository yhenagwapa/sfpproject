<?php

namespace App\Jobs;

use App\Models\ReportQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reportQueueId;

    /**
     * Create a new job instance.
     */
    public function __construct($reportQueueId)
    {
        $this->reportQueueId = $reportQueueId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reportQueue = ReportQueue::find($this->reportQueueId);

        if (!$reportQueue) {
            return;
        }

        try {
            // Update status to generating
            $reportQueue->update(['status' => 'generating']);

            // Call the appropriate report generation method
            $method = 'generate' . ucfirst($reportQueue->report) . 'Report';

            // Here you'll call your actual report generation logic
            // For now, just a placeholder
            sleep(2); // Simulate report generation

            // Update status to ready
            $reportQueue->update([
                'status' => 'ready',
                'generated_at' => now(),
                'file_path' => 'reports/' . $reportQueue->report . '_' . time() . '.pdf', // example
            ]);

        } catch (\Exception $e) {
            $reportQueue->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
