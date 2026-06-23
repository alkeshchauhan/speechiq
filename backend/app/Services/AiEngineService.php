<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AiEngineService
{
    /**
     * Call the FastAPI Python engine to analyze a Read Aloud recording.
     */
    public function analyzeReadAloud(string $audioPath, string $targetText, float $duration = 0.0): array
    {
        $apiUrl = setting('AI_API_URL', 'http://127.0.0.1:8001');
        $apiToken = setting('AI_API_TOKEN');

        $fullPath = Storage::disk('public')->path($audioPath);

        if (!file_exists($fullPath)) {
            Log::error("Audio file does not exist at " . $fullPath);
            throw new \Exception("Recorded audio file not found on disk.");
        }

        try {
            $request = Http::withoutVerifying()->timeout(90)->withHeaders($this->getAiHeaders());
            
            if (!empty($apiToken)) {
                $request = $request->withToken($apiToken);
            }

            // Send file and expected text as multipart
            $response = $request->attach(
                'audio_file',
                file_get_contents($fullPath),
                basename($fullPath)
            )->post(rtrim($apiUrl, '/') . '/read-aloud-analyze', [
                'expected_text' => $targetText,
                'duration' => $duration
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("AI Engine responded with error status " . $response->status() . ": " . $response->body());
            throw new \Exception("AI Engine API error: " . $response->body());

        } catch (\Exception $e) {
            Log::error("Request to AI Engine failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Call the FastAPI Python engine to convert speech to text (STT).
     */
    public function speechToText(string $audioPath): string
    {
        $apiUrl = setting('AI_API_URL', 'http://127.0.0.1:8001');
        $apiToken = setting('AI_API_TOKEN');
        $fullPath = Storage::disk('public')->path($audioPath);

        if (!file_exists($fullPath)) {
            throw new \Exception("Audio file not found on disk: " . $fullPath);
        }

        try {
            $request = Http::withoutVerifying()->timeout(90)->withHeaders($this->getAiHeaders());
            if (!empty($apiToken)) {
                $request = $request->withToken($apiToken);
            }

            $response = $request->attach(
                'audio_file',
                file_get_contents($fullPath),
                basename($fullPath)
            )->post(rtrim($apiUrl, '/') . '/speech-to-text');

            if ($response->successful()) {
                return $response->json()['text'] ?? '';
            }

            throw new \Exception("AI Engine STT error: " . $response->body());
        } catch (\Exception $e) {
            Log::error("speechToText failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Call the FastAPI Python engine to analyze an Interview voice response.
     */
    public function analyzeInterview(string $audioPath, string $questionText, float $duration = 0.0): array
    {
        $apiUrl = setting('AI_API_URL', 'http://127.0.0.1:8001');
        $apiToken = setting('AI_API_TOKEN');
        $fullPath = Storage::disk('public')->path($audioPath);

        if (!file_exists($fullPath)) {
            throw new \Exception("Audio file not found on disk: " . $fullPath);
        }

        try {
            $request = Http::withoutVerifying()->timeout(90)->withHeaders($this->getAiHeaders());
            if (!empty($apiToken)) {
                $request = $request->withToken($apiToken);
            }

            $response = $request->attach(
                'audio_file',
                file_get_contents($fullPath),
                basename($fullPath)
            )->post(rtrim($apiUrl, '/') . '/interview-analyze', [
                'question' => $questionText,
                'duration' => $duration
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception("AI Engine Interview analysis error: " . $response->body());
        } catch (\Exception $e) {
            Log::error("analyzeInterview failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Call the FastAPI Python engine to generate the next interview question.
     */
    public function generateQuestion(string $context, array $history = []): string
    {
        $apiUrl = setting('AI_API_URL', 'http://127.0.0.1:8001');
        $apiToken = setting('AI_API_TOKEN');

        try {
            $request = Http::withoutVerifying()->timeout(90)->withHeaders($this->getAiHeaders());
            if (!empty($apiToken)) {
                $request = $request->withToken($apiToken);
            }

            $response = $request->post(rtrim($apiUrl, '/') . '/generate-question', [
                'context' => $context,
                'history' => $history
            ]);

            if ($response->successful()) {
                return $response->json()['question'] ?? '';
            }

            throw new \Exception("AI Engine generate-question error: " . $response->body());
        } catch (\Exception $e) {
            Log::error("generateQuestion failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Call the FastAPI Python engine to generate summary feedback.
     */
    public function generateFeedback(string $question, string $transcript): string
    {
        $apiUrl = setting('AI_API_URL', 'http://127.0.0.1:8001');
        $apiToken = setting('AI_API_TOKEN');

        try {
            $request = Http::withoutVerifying()->timeout(90)->withHeaders($this->getAiHeaders());
            if (!empty($apiToken)) {
                $request = $request->withToken($apiToken);
            }

            $response = $request->post(rtrim($apiUrl, '/') . '/generate-feedback', [
                'question' => $question,
                'transcript' => $transcript
            ]);

            if ($response->successful()) {
                return $response->json()['feedback'] ?? '';
            }

            throw new \Exception("AI Engine generate-feedback error: " . $response->body());
        } catch (\Exception $e) {
            Log::error("generateFeedback failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Call the FastAPI Python engine to synthesize text to speech (TTS).
     * Returns a base64 data URI that can be set directly as an audio src.
     */
    public function textToSpeech(string $text): string
    {
        $apiUrl = setting('AI_API_URL', 'http://127.0.0.1:8001');
        $apiToken = setting('AI_API_TOKEN');

        try {
            $request = Http::withoutVerifying()->timeout(30)->withHeaders($this->getAiHeaders());
            if (!empty($apiToken)) {
                $request = $request->withToken($apiToken);
            }

            $response = $request->post(rtrim($apiUrl, '/') . '/text-to-speech', [
                'text' => $text
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // New real TTS: returns base64-encoded audio
                if (!empty($data['audio_base64'])) {
                    $mimeType = $data['mime_type'] ?? 'audio/mpeg';
                    return 'data:' . $mimeType . ';base64,' . $data['audio_base64'];
                }

                // Legacy fallback: returns audio_url
                return $data['audio_url'] ?? '';
            }

            throw new \Exception("AI Engine text-to-speech error: " . $response->body());
        } catch (\Exception $e) {
            Log::error("textToSpeech failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get API credentials and settings from settings table.
     */
    protected function getAiHeaders(): array
    {
        return [
            'X-Gemini-Key'   => setting('GEMINI_API_KEY') ?: '',
            'X-Gemini-Model' => setting('GEMINI_MODEL', 'gemini-2.5-flash'),
            'X-Use-Gemini'   => '1',
        ];
    }
}
