<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Setting;
use App\Services\AiEngineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FastApiIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected AiEngineService $aiEngineService;
    protected string $mockAudioPath = 'recordings/test_voice.webm';

    protected function setUp(): void
    {
        parent::setUp();

        // Register default user role
        $role = Role::create(['name' => 'User', 'slug' => 'user']);
        $user = User::factory()->create();
        $user->roles()->attach($role->id);

        // Seed settings via SettingService to handle cache eviction properly
        $settingService = resolve(\App\Services\SettingService::class);
        $settingService->set('AI_API_URL', 'http://127.0.0.1:8000');
        $settingService->set('AI_API_TOKEN', 'custom-jwt-token');

        // Resolve service
        $this->aiEngineService = resolve(AiEngineService::class);

        // Put a fake audio file on public disk to bypass file_exists check
        Storage::fake('public');
        Storage::disk('public')->put($this->mockAudioPath, 'fake-audio-binary-payload');
    }

    /**
     * Test speechToText integration.
     */
    public function test_speech_to_text_sends_correct_payload(): void
    {
        Http::fake([
            'http://127.0.0.1:8000/speech-to-text' => Http::response([
                'text' => 'This is simulated speech transcript'
            ], 200)
        ]);

        $transcript = $this->aiEngineService->speechToText($this->mockAudioPath);

        $this->assertEquals('This is simulated speech transcript', $transcript);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://127.0.0.1:8000/speech-to-text' &&
                   $request->hasHeader('Authorization', 'Bearer custom-jwt-token') &&
                   $request->isMultipart();
        });
    }

    /**
     * Test analyzeReadAloud integration.
     */
    public function test_analyze_read_aloud_sends_correct_payload(): void
    {
        Http::fake([
            'http://127.0.0.1:8000/read-aloud-analyze' => Http::response([
                'transcript' => 'read aloud text',
                'overall_score' => 85,
                'correct_words' => ['read', 'aloud', 'text'],
                'similarity_percentage' => 100.00,
                'confidence_score' => 90,
                'speech_rate' => 2.50,
                'long_pauses' => 1
            ], 200)
        ]);

        $result = $this->aiEngineService->analyzeReadAloud($this->mockAudioPath, 'Target paragraph text');

        $this->assertEquals(85, $result['overall_score']);
        $this->assertEquals('read aloud text', $result['transcript']);
        $this->assertEquals(['read', 'aloud', 'text'], $result['correct_words']);
        $this->assertEquals(100.00, $result['similarity_percentage']);
        $this->assertEquals(90, $result['confidence_score']);
        $this->assertEquals(2.50, $result['speech_rate']);
        $this->assertEquals(1, $result['long_pauses']);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://127.0.0.1:8000/read-aloud-analyze' &&
                   $request->hasHeader('Authorization', 'Bearer custom-jwt-token') &&
                   $request->isMultipart();
        });
    }

    /**
     * Test analyzeInterview integration.
     */
    public function test_analyze_interview_sends_correct_payload(): void
    {
        Http::fake([
            'http://127.0.0.1:8000/interview-analyze' => Http::response([
                'transcript' => 'interview response text',
                'overall_score' => 90,
                'feedback' => 'excellent pacing',
                'tone' => 'Confident',
                'communication_score' => 85,
                'wpm' => 135,
                'language' => 'English',
                'confidence' => 92,
                'pronunciation' => 80,
                'fluency' => 85,
                'grammar' => 90,
                'vocabulary' => 85,
                'overall' => 90,
            ], 200)
        ]);

        $result = $this->aiEngineService->analyzeInterview($this->mockAudioPath, 'Active question context');

        $this->assertEquals(90, $result['overall_score']);
        $this->assertEquals('excellent pacing', $result['feedback']);
        $this->assertEquals('Confident', $result['tone']);
        $this->assertEquals(85, $result['communication_score']);
        $this->assertEquals(135, $result['wpm']);
        $this->assertEquals('English', $result['language']);
        $this->assertEquals(92, $result['confidence']);
        $this->assertEquals(80, $result['pronunciation']);
        $this->assertEquals(85, $result['fluency']);
        $this->assertEquals(90, $result['grammar']);
        $this->assertEquals(85, $result['vocabulary']);
        $this->assertEquals(90, $result['overall']);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://127.0.0.1:8000/interview-analyze' &&
                   $request->hasHeader('Authorization', 'Bearer custom-jwt-token') &&
                   $request->isMultipart();
        });
    }

    /**
     * Test generateQuestion integration.
     */
    public function test_generate_question_sends_correct_payload(): void
    {
        Http::fake([
            'http://127.0.0.1:8000/generate-question' => Http::response([
                'question' => 'How do you configure databases?'
            ], 200)
        ]);

        $history = [
            ['role' => 'assistant', 'content' => 'Tell me about yourself.'],
            ['role' => 'user', 'content' => 'I am a programmer.']
        ];

        $question = $this->aiEngineService->generateQuestion('Tech context prompt', $history);

        $this->assertEquals('How do you configure databases?', $question);

        Http::assertSent(function ($request) use ($history) {
            return $request->url() === 'http://127.0.0.1:8000/generate-question' &&
                   $request->hasHeader('Authorization', 'Bearer custom-jwt-token') &&
                   $request['context'] === 'Tech context prompt' &&
                   $request['history'] === $history;
        });
    }

    /**
     * Test generateFeedback integration.
     */
    public function test_generate_feedback_sends_correct_payload(): void
    {
        Http::fake([
            'http://127.0.0.1:8000/generate-feedback' => Http::response([
                'feedback' => 'Vocabulary is strong.'
            ], 200)
        ]);

        $feedback = $this->aiEngineService->generateFeedback('What is OOP?', 'OOP is programming style.');

        $this->assertEquals('Vocabulary is strong.', $feedback);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://127.0.0.1:8000/generate-feedback' &&
                   $request->hasHeader('Authorization', 'Bearer custom-jwt-token') &&
                   $request['question'] === 'What is OOP?' &&
                   $request['transcript'] === 'OOP is programming style.';
        });
    }

    /**
     * Test textToSpeech integration.
     */
    public function test_text_to_speech_sends_correct_payload(): void
    {
        Http::fake([
            'http://127.0.0.1:8000/text-to-speech' => Http::response([
                'audio_url' => '/recordings/synth.mp3'
            ], 200)
        ]);

        $audioUrl = $this->aiEngineService->textToSpeech('Synthesize text');

        $this->assertEquals('/recordings/synth.mp3', $audioUrl);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://127.0.0.1:8000/text-to-speech' &&
                   $request->hasHeader('Authorization', 'Bearer custom-jwt-token') &&
                   $request['text'] === 'Synthesize text';
        });
    }
}
