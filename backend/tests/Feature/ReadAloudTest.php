<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Test;
use App\Models\Question;
use App\Models\AudioRecording;
use App\Models\ReadAloudResult;
use App\Jobs\AnalyzeReadAloudJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReadAloudTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_can_view_read_aloud_index(): void
    {
        $user = User::factory()->create();
        
        $test = Test::create([
            'title' => 'Sample Read Aloud',
            'description' => 'Test description',
            'type' => 'READ_ALOUD',
            'is_active' => true
        ]);

        $response = $this->actingAs($user)->get(route('practice.read-aloud.index'));

        $response->assertStatus(200);
        $response->assertSee('Sample Read Aloud');
    }

    public function test_candidate_can_view_read_aloud_test_show(): void
    {
        $user = User::factory()->create();
        
        $test = Test::create([
            'title' => 'Sample Read Aloud Test',
            'description' => 'Test description',
            'type' => 'READ_ALOUD',
            'is_active' => true
        ]);

        $question = Question::create([
            'test_id' => $test->id,
            'question_text' => 'Read this sentence aloud for the speech test.',
            'question_type' => 'READ_ALOUD',
            'sort_order' => 1
        ]);

        $response = $this->actingAs($user)->get(route('practice.read-aloud.show', $test->id));

        $response->assertStatus(200);
        $response->assertSee('Read this sentence aloud for the speech test.');
    }

    public function test_candidate_can_submit_recording(): void
    {
        Storage::fake('public');
        Queue::fake();

        $user = User::factory()->create();
        
        $test = Test::create([
            'title' => 'Sample Read Aloud Test',
            'description' => 'Test description',
            'type' => 'READ_ALOUD',
            'is_active' => true
        ]);

        $question = Question::create([
            'test_id' => $test->id,
            'question_text' => 'Read this sentence aloud for the speech test.',
            'question_type' => 'READ_ALOUD',
            'sort_order' => 1
        ]);

        $file = UploadedFile::fake()->create('recording.webm', 100, 'audio/webm');

        $response = $this->actingAs($user)->postJson(route('practice.read-aloud.submit', [$test->id, $question->id]), [
            'audio_file' => $file,
            'duration' => 8.5,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Recording uploaded! Analyzing your voice...'
            ]);

        $this->assertDatabaseHas('audio_recordings', [
            'user_id' => $user->id,
            'question_id' => $question->id,
            'duration' => 8.5,
            'status' => 'pending',
        ]);

        Queue::assertPushed(AnalyzeReadAloudJob::class);
    }

    public function test_candidate_can_poll_status(): void
    {
        $user = User::factory()->create();
        
        $test = Test::create([
            'title' => 'Sample Read Aloud Test',
            'type' => 'READ_ALOUD',
            'is_active' => true
        ]);

        $question = Question::create([
            'test_id' => $test->id,
            'question_text' => 'Read this sentence.',
            'question_type' => 'READ_ALOUD',
        ]);

        $recording = AudioRecording::create([
            'user_id' => $user->id,
            'question_id' => $question->id,
            'audio_path' => 'recordings/test.webm',
            'duration' => 5.0,
            'status' => 'processing'
        ]);

        $response = $this->actingAs($user)->getJson(route('practice.read-aloud.status', $recording->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'processing',
                'result_id' => null
            ]);
    }

    public function test_candidate_can_view_results_page(): void
    {
        $user = User::factory()->create();
        
        $test = Test::create([
            'title' => 'Sample Read Aloud Test',
            'type' => 'READ_ALOUD',
            'is_active' => true
        ]);

        $question = Question::create([
            'test_id' => $test->id,
            'question_text' => 'Read this sentence.',
            'question_type' => 'READ_ALOUD',
        ]);

        $recording = AudioRecording::create([
            'user_id' => $user->id,
            'question_id' => $question->id,
            'audio_path' => 'recordings/test.webm',
            'duration' => 5.0,
            'status' => 'completed'
        ]);

        $result = ReadAloudResult::create([
            'audio_recording_id' => $recording->id,
            'transcript' => 'read this sentence',
            'pronunciation_score' => 90,
            'fluency_score' => 85,
            'accuracy_score' => 95,
            'wpm' => 140,
            'pause_count' => 1,
            'pause_duration' => 0.8,
            'missing_words' => [],
            'extra_words' => [],
            'accent' => 'US Accent',
            'overall_score' => 90,
            'correct_words' => ['read', 'this', 'sentence'],
            'similarity_percentage' => 100.00,
            'confidence_score' => 88,
            'speech_rate' => 2.10,
            'long_pauses' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('practice.read-aloud.results', $recording->id));

        $response->assertStatus(200);
        $response->assertSee('Sample Read Aloud Test');
        $response->assertSee('90');
        $response->assertSee('85%');
        $response->assertSee('95%');
        $response->assertSee('Confidence Estimate');
        $response->assertSee('Text Similarity');
        $response->assertSee('88%');
        $response->assertSee('100%');
        $response->assertSee('Speech rate:');
        $response->assertSee('2.10 words/sec');
        $response->assertSee('Long pauses');
        $response->assertSee('1.5s');
        $response->assertSee('0 times');
    }

    public function test_low_similarity_results_page(): void
    {
        $user = User::factory()->create();
        
        $test = Test::create([
            'title' => 'Sample Read Aloud Test',
            'type' => 'READ_ALOUD',
            'is_active' => true
        ]);

        $question = Question::create([
            'test_id' => $test->id,
            'question_text' => 'Read this sentence.',
            'question_type' => 'READ_ALOUD',
        ]);

        $recording = AudioRecording::create([
            'user_id' => $user->id,
            'question_id' => $question->id,
            'audio_path' => 'recordings/test.webm',
            'duration' => 5.0,
            'status' => 'completed'
        ]);

        $result = ReadAloudResult::create([
            'audio_recording_id' => $recording->id,
            'transcript' => 'abcd',
            'pronunciation_score' => 5,
            'fluency_score' => 10,
            'accuracy_score' => 0,
            'wpm' => 12,
            'pause_count' => 0,
            'pause_duration' => 0.0,
            'missing_words' => ['read', 'this', 'sentence'],
            'extra_words' => ['abcd'],
            'accent' => 'None',
            'overall_score' => 3,
            'correct_words' => [],
            'similarity_percentage' => 0.00,
            'confidence_score' => 0,
            'speech_rate' => 0.20,
            'long_pauses' => 0,
        ]);

        $response = $this->actingAs($user)->get(route('practice.read-aloud.results', $recording->id));

        $response->assertStatus(200);
        $response->assertSee('Sample Read Aloud Test');
        $response->assertSee('3');
        $response->assertSee('5%');
        $response->assertSee('10%');
        $response->assertSee('0%');
        $response->assertSee('Text Similarity');
        $response->assertSee('Speech rate:');
        $response->assertSee('0.20 words/sec');
        $response->assertSee('Long pauses');
        $response->assertSee('0 times');
    }
}
