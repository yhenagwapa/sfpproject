<?php

namespace App\Jobs;

use App\Models\ReportQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

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

            // Call the appropriate artisan command based on report type
            if ($reportQueue->report === 'masterlist') {
                Artisan::call('reports:masterlist', [
                    'user_id' => $reportQueue->user_id,
                    'cdc_id'  => $reportQueue->cdc_id
                ]);
            }
            elseif ($reportQueue->report === 'age-bracket-upon-entry') {
                Artisan::call('reports:age-bracket-upon-entry', [
                    'user_id' => $reportQueue->user_id,
                    'cdc_id'  => $reportQueue->cdc_id
                ]);
            }
            elseif ($reportQueue->report === 'age-bracket-after-120') {
                Artisan::call('reports:age-bracket-after-120', [
                    'user_id' => $reportQueue->user_id,
                    'cdc_id'  => $reportQueue->cdc_id
                ]);
            }
            elseif ($reportQueue->report === 'monitoring') {
                Artisan::call('reports:monitoring', [
                    'user_id' => $reportQueue->user_id,
                    'cdc_id'  => $reportQueue->cdc_id
                ]);
            }
            elseif ($reportQueue->report === 'undernourished-upon-entry') {
                Artisan::call('reports:undernourished-upon-entry', [
                    'user_id' => $reportQueue->user_id,
                ]);
            }
            elseif ($reportQueue->report === 'undernourished-after-120') {
                Artisan::call('reports:undernourished-after-120', [
                    'user_id' => $reportQueue->user_id,
                ]);
            }
             elseif ($reportQueue->report === 'disabilities') {
                Artisan::call('reports:disabilities', [
                    'user_id' => $reportQueue->user_id,
                ]);
            }
            elseif ($reportQueue->report === 'malnourished') {
                Artisan::call('reports:malnourished', [
                    'user_id' => $reportQueue->user_id,
                ]);
            }
            elseif ($reportQueue->report === 'unfunded') {
                Artisan::call('reports:unfunded', [
                    'user_id' => $reportQueue->user_id,
                    'cdc_id'  => $reportQueue->cdc_id
                ]);
            }

            // Update status to ready
            $reportQueue->update([
                'status' => 'ready',
                'generated_at' => now(),
            ]);

        } catch (\Exception $e) {
            $reportQueue->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
