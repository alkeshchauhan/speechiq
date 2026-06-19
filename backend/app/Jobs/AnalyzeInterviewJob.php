<?php

namespace App\Jobs;

use App\Models\AudioRecording;
use App\Services\AiEngineService;
use App\Repositories\Contracts\InterviewResultRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeInterviewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected AudioRecording $audioRecording;
    protected string $questionText;

    /**
     * Create a new job instance.
     */
    public function __construct(AudioRecording $audioRecording, string $questionText)
    {
        $this->audioRecording = $audioRecording;
        $this->questionText = $questionText;
    }

    /**
     * Execute the job.
     */
    public function handle(AiEngineService $aiEngineService, InterviewResultRepositoryInterface $resultRepo): void
    {
        $this->audioRecording->update(['status' => 'processing']);

        try {
            $analysis = $aiEngineService->analyzeInterview(
                $this->audioRecording->audio_path,
                $this->questionText,
                (float) $this->audioRecording->duration
            );

            // Create InterviewResult record
            $resultRepo->create([
                'audio_recording_id' => $this->audioRecording->id,
                'question' => $this->questionText,
                'transcript' => $analysis['transcript'] ?? '',
                'grammar_score' => $analysis['grammar_score'] ?? 0,
                'vocabulary_score' => $analysis['vocabulary_score'] ?? 0,
                'content_score' => $analysis['content_score'] ?? 0,
                'communication_score' => $analysis['communication_score'] ?? 0,
                'confidence_score' => $analysis['confidence_score'] ?? 0,
                'pronunciation_score' => $analysis['pronunciation_score'] ?? 0,
                'fluency_score' => $analysis['fluency_score'] ?? 0,
                'accent' => $analysis['accent'] ?? '',
                'overall_score' => $analysis['overall_score'] ?? 0,
                'feedback' => $analysis['feedback'] ?? '',
                'tone' => $analysis['tone'] ?? 'Professional',
                'wpm' => $analysis['wpm'] ?? 0,
            ]);

            $this->audioRecording->update(['status' => 'completed']);
            \App\Jobs\GenerateReportJob::dispatch($this->audioRecording->user_id);

        } catch (\Exception $e) {
            Log::error("AnalyzeInterviewJob failed for Recording ID {$this->audioRecording->id}: " . $e->getMessage());
            $this->audioRecording->update(['status' => 'failed']);
            throw $e;
        }
    }
}
