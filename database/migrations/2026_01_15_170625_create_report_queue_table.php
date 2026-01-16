<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReportQueue;

class ReportController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:generate {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate various types of reports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');

        $method = 'get' . ucfirst($type);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        $this->error("Report type '{$type}' not found.");
        return 1;
    }

    /**
     * Generate masterlist report
     */
    protected function getMasterlist()
    {
        // Get the oldest pending masterlist report from the queue
        $report = ReportQueue::where('report', 'masterlist')
            ->where('status', 'pending')
            ->oldest()
            ->first();

        if (!$report) {
            $this->info('No pending masterlist reports in the queue.');
            return 0;
        }

        // Update status to generating
        $report->update(['status' => 'generating']);

        // Display the report details
        $this->info('Processing Masterlist Report:');
        $this->newLine();
        $this->line("Report ID: {$report->id}");
        $this->line("User ID: {$report->user_id}");
        $this->line("Report Type: {$report->report}");
        $this->line("Status: {$report->status}");
        $this->line("Created At: {$report->created_at}");
        $this->newLine();

        // Update status to ready
        $report->update([
            'status' => 'ready',
            'generated_at' => now(),
        ]);

        $this->info('Report generation completed!');

        return 0;
    }
}
