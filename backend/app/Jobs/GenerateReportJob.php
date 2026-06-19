<?php

namespace App\Jobs;

use App\Mail\AnalysisCompleteNotification;
use App\Models\User;
use App\Services\AnalysisReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int    $userId;
    protected string $moduleType;
    protected int    $overallScore;
    protected string $resultsUrl;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, string $moduleType = 'Read Aloud', int $overallScore = 0, string $resultsUrl = '')
    {
        $this->userId       = $userId;
        $this->moduleType   = $moduleType;
        $this->overallScore = $overallScore;
        $this->resultsUrl   = $resultsUrl;
    }

    /**
     * Execute the job.
     */
    public function handle(AnalysisReportService $reportService): void
    {
        try {
            $reportService->generateUserReport($this->userId);
            Log::info("GenerateReportJob: Successfully compiled metrics for User ID {$this->userId}");
        } catch (\Exception $e) {
            Log::error("GenerateReportJob failed for User ID {$this->userId}: " . $e->getMessage());
            throw $e;
        }

        // Send email notification (non-fatal if it fails)
        try {
            $user = User::find($this->userId);
            if ($user && $user->email) {
                Mail::to($user->email)->send(new AnalysisCompleteNotification(
                    userName:     $user->name,
                    moduleType:   $this->moduleType,
                    overallScore: $this->overallScore,
                    resultsUrl:   $this->resultsUrl ?: route('dashboard'),
                ));
                Log::info("Analysis complete email sent to {$user->email}");
            }
        } catch (\Exception $e) {
            // Mail failure must never break the job
            Log::warning("Failed to send analysis complete email for User ID {$this->userId}: " . $e->getMessage());
        }
    }
}
