<?php

namespace App\Jobs;

use App\Models\AudioRecording;
use App\Services\AiEngineService;
use App\Repositories\Contracts\ReadAloudResultRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeReadAloudJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected AudioRecording $audioRecording;
    protected string $targetText;

    /**
     * Create a new job instance.
     */
    public function __construct(AudioRecording $audioRecording, string $targetText)
    {
        $this->audioRecording = $audioRecording;
        $this->targetText = $targetText;
    }

    /**
     * Execute the job.
     */
    public function handle(AiEngineService $aiEngineService, ReadAloudResultRepositoryInterface $resultRepo): void
    {
        $this->audioRecording->update(['status' => 'processing']);

        try {
            $analysis = $aiEngineService->analyzeReadAloud(
                $this->audioRecording->audio_path,
                $this->targetText,
                (float) $this->audioRecording->duration
            );

            // Create ReadAloudResult record
            $resultRepo->create([
                'audio_recording_id' => $this->audioRecording->id,
                'transcript' => $analysis['transcript'] ?? '',
                'pronunciation_score' => $analysis['pronunciation_score'] ?? 0,
                'fluency_score' => $analysis['fluency_score'] ?? 0,
                'accuracy_score' => $analysis['accuracy_score'] ?? 0,
                'wpm' => $analysis['wpm'] ?? 0,
                'pause_count' => $analysis['pause_count'] ?? 0,
                'pause_duration' => $analysis['pause_duration'] ?? 0,
                'missing_words' => $analysis['missing_words'] ?? [],
                'extra_words' => $analysis['extra_words'] ?? [],
                'accent' => $analysis['accent'] ?? '',
                'overall_score' => $analysis['overall_score'] ?? 0,
                'correct_words' => $analysis['correct_words'] ?? [],
                'similarity_percentage' => $analysis['similarity_percentage'] ?? 0.00,
                'confidence_score' => $analysis['confidence_score'] ?? 0,
                'speech_rate' => $analysis['speech_rate'] ?? 0.00,
                'long_pauses' => $analysis['long_pauses'] ?? 0,
            ]);

            $this->audioRecording->update(['status' => 'completed']);

            $overallScore = (int) ($analysis['overall_score'] ?? 0);
            $resultsUrl   = route('practice.read-aloud.results', $this->audioRecording->id);
            \App\Jobs\GenerateReportJob::dispatch($this->audioRecording->user_id, 'Read Aloud', $overallScore, $resultsUrl);

        } catch (\Exception $e) {
            Log::error("AnalyzeReadAloudJob failed for Recording ID {$this->audioRecording->id}: " . $e->getMessage());
            $this->audioRecording->update(['status' => 'failed']);
            throw $e; // Throw exception to trigger queue retry/fail tracking
        }
    }
}
