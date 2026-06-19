<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Test;
use App\Models\TestSection;
use App\Models\Question;
use App\Models\AudioRecording;
use App\Models\InterviewResult;
use App\Jobs\AnalyzeInterviewJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InterviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $candidate;
    protected Test $test;
    protected Question $question;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user role and user
        $role = Role::create(['name' => 'User', 'slug' => 'user']);
        $this->candidate = User::factory()->create();
        $this->candidate->roles()->attach($role->id);

        // Create settings via SettingService to handle cache eviction properly
        $settingService = resolve(\App\Services\SettingService::class);
        $settingService->set('AI_API_URL', 'http://127.0.0.1:8000');
        $settingService->set('AI_API_TOKEN', 'mock-token');

        // Create AI Interview Test
        $this->test = Test::create([
            'title' => 'Software Engineer Tech Interview',
            'description' => 'Technical assessment',
            'type' => 'AI_INTERVIEW',
            'is_active' => true
        ]);

        $section = TestSection::create([
            'test_id' => $this->test->id,
            'title' => 'Architecture',
            'sort_order' => 1
        ]);

        $this->question = Question::create([
            'test_id' => $this->test->id,
            'test_section_id' => $section->id,
            'question_text' => 'Software Engineering role. Starting query: Tell me about yourself.',
            'question_type' => 'AI_INTERVIEW',
            'sort_order' => 1
        ]);
    }

    /**
     * Test candidate dashboard can list active AI Interview tests.
     */
    public function test_candidate_can_view_interview_dashboard(): void
    {
        $response = $this->actingAs($this->candidate)->get(route('practice.interview.index'));

        $response->assertStatus(200);
        $response->assertViewHas('tests');
    }

    /**
     * Test candidate can access specific interview page.
     */
    public function test_candidate_can_view_interview_page(): void
    {
        $response = $this->actingAs($this->candidate)->get(route('practice.interview.show', $this->test->id));

        $response->assertStatus(200);
        $response->assertSee('Software Engineer Tech Interview');
    }

    /**
     * Test submit voice answer dispatches background AnalyzeInterviewJob.
     */
    public function test_submitting_audio_dispatches_queue_job(): void
    {
        Queue::fake();

        $audio = UploadedFile::fake()->create('response.webm', 500, 'audio/webm');

        $response = $this->actingAs($this->candidate)->post(route('practice.interview.submit', $this->test->id), [
            'audio_file' => $audio,
            'duration' => 12.5,
            'question_text' => 'Tell me about yourself.'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'recording_id', 'message']);

        // Check DB entry
        $this->assertDatabaseHas('audio_recordings', [
            'user_id' => $this->candidate->id,
            'duration' => 12.5,
            'status' => 'pending'
        ]);

        // Assert job pushed in Laravel 12
        Queue::assertPushed(AnalyzeInterviewJob::class);
    }

    /**
     * Test status route returns correct processing status.
     */
    public function test_status_endpoint_returns_recording_status(): void
    {
        $recording = AudioRecording::create([
            'user_id' => $this->candidate->id,
            'question_id' => $this->question->id,
            'audio_path' => 'recordings/mock.webm',
            'duration' => 10.0,
            'status' => 'processing'
        ]);

        $response = $this->actingAs($this->candidate)->get(route('practice.interview.status', $recording->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'processing'
        ]);
    }

    /**
     * Test result returns score metrics database details.
     */
    public function test_results_endpoint_returns_evaluation_scores(): void
    {
        $recording = AudioRecording::create([
            'user_id' => $this->candidate->id,
            'question_id' => $this->question->id,
            'audio_path' => 'recordings/mock.webm',
            'duration' => 10.0,
            'status' => 'completed'
        ]);

        $result = InterviewResult::create([
            'audio_recording_id' => $recording->id,
            'question' => 'Tell me about yourself.',
            'transcript' => 'Mock transcript response.',
            'grammar_score' => 90,
            'vocabulary_score' => 85,
            'content_score' => 88,
            'communication_score' => 88,
            'confidence_score' => 92,
            'pronunciation_score' => 80,
            'fluency_score' => 85,
            'accent' => 'US Accent',
            'overall_score' => 86,
            'feedback' => 'Good job.',
            'tone' => 'Professional',
            'wpm' => 140,
        ]);

        $response = $this->actingAs($this->candidate)->get(route('practice.interview.results', $recording->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'result' => [
                'overall_score' => 86,
                'accent' => 'US Accent',
                'tone' => 'Professional',
                'wpm' => 140,
                'communication_score' => 88,
            ]
        ]);
    }

    /**
     * Test candidate can view HTML results page.
     */
    public function test_candidate_can_view_results_page(): void
    {
        $recording = AudioRecording::create([
            'user_id' => $this->candidate->id,
            'question_id' => $this->question->id,
            'audio_path' => 'recordings/mock.webm',
            'duration' => 10.0,
            'status' => 'completed'
        ]);

        $result = InterviewResult::create([
            'audio_recording_id' => $recording->id,
            'question' => 'Tell me about yourself.',
            'transcript' => 'Mock transcript response.',
            'grammar_score' => 90,
            'vocabulary_score' => 85,
            'content_score' => 88,
            'communication_score' => 88,
            'confidence_score' => 92,
            'pronunciation_score' => 80,
            'fluency_score' => 85,
            'accent' => 'US Accent',
            'overall_score' => 86,
            'feedback' => 'Good job.',
            'tone' => 'Professional',
            'wpm' => 140,
        ]);

        $response = $this->actingAs($this->candidate)->get(route('practice.interview.results-view', $recording->id));

        $response->assertStatus(200);
        $response->assertSee('Interview Response Analysis');
        $response->assertSee('Mock transcript response.');
        $response->assertSee('Good job.');
    }

    /**
     * Test dynamic question generation handles connection cleanly.
     */
    public function test_generating_next_question_contacts_fastapi(): void
    {
        // Fake FastAPI POST /generate-question call
        Http::fake([
            'http://127.0.0.1:8000/generate-question' => Http::response([
                'question' => 'How do you handle scaling bottlenecks in database transactions?'
            ], 200)
        ]);

        $response = $this->actingAs($this->candidate)->post(route('practice.interview.next-question', $this->test->id), [
            'context' => 'Technical background role',
            'history' => [
                ['role' => 'assistant', 'content' => 'Tell me about yourself.'],
                ['role' => 'user', 'content' => 'I am a Laravel coder.']
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'question' => 'How do you handle scaling bottlenecks in database transactions?'
        ]);
    }

    public function test_gibberish_response_capping_interview(): void
    {
        $recording = AudioRecording::create([
            'user_id' => $this->candidate->id,
            'question_id' => $this->question->id,
            'audio_path' => 'recordings/mock.webm',
            'duration' => 10.0,
            'status' => 'completed'
        ]);

        $result = InterviewResult::create([
            'audio_recording_id' => $recording->id,
            'question' => 'Tell me about yourself.',
            'transcript' => 'abcd',
            'grammar_score' => 0,
            'vocabulary_score' => 0,
            'content_score' => 0,
            'communication_score' => 0,
            'confidence_score' => 5,
            'pronunciation_score' => 5,
            'fluency_score' => 5,
            'accent' => 'None',
            'overall_score' => 2,
            'feedback' => 'Unrelated words or gibberish detected. Please record a clear spoken response to the question asked.',
            'tone' => 'None',
            'wpm' => 6,
        ]);

        $response = $this->actingAs($this->candidate)->get(route('practice.interview.results-view', $recording->id));

        $response->assertStatus(200);
        $response->assertSee('Interview Response Analysis');
        $response->assertSee('abcd');
        $response->assertSee('Unrelated words or gibberish detected.');
        $response->assertSee('2');
    }
}

