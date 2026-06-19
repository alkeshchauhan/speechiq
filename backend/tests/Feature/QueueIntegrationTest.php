<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Test;
use App\Models\Question;
use App\Models\AudioRecording;
use App\Jobs\AnalyzeReadAloudJob;
use App\Jobs\AnalyzeInterviewJob;
use App\Jobs\GenerateReportJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Tests\TestCase;

class QueueIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $candidate;
    protected Question $question;
    protected AudioRecording $recording;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles & users
        $role = Role::create(['name' => 'User', 'slug' => 'user']);
        $this->candidate = User::factory()->create();
        $this->candidate->roles()->attach($role->id);

        $test = Test::create(['title' => 'Sample test', 'type' => 'READ_ALOUD', 'is_active' => true]);
        $this->question = Question::create([
            'test_id' => $test->id,
            'question_text' => 'Read this text aloud.',
            'question_type' => 'READ_ALOUD',
            'sort_order' => 1
        ]);

        $this->recording = AudioRecording::create([
            'user_id' => $this->candidate->id,
            'question_id' => $this->question->id,
            'audio_path' => 'recordings/r1.webm',
            'duration' => 10.0,
            'status' => 'pending'
        ]);
    }

    /**
     * Test AnalyzeReadAloudJob complies with queue contract.
     */
    public function test_analyze_read_aloud_job_implements_should_queue(): void
    {
        $job = new AnalyzeReadAloudJob($this->recording, 'Read this text aloud.');
        
        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    /**
     * Test AnalyzeInterviewJob complies with queue contract.
     */
    public function test_analyze_interview_job_implements_should_queue(): void
    {
        $job = new AnalyzeInterviewJob($this->recording, 'What is OOP?');
        
        $this->assertInstanceOf(ShouldQueue::class, $job);
    }

    /**
     * Test GenerateReportJob complies with queue contract.
     */
    public function test_generate_report_job_implements_should_queue(): void
    {
        $job = new GenerateReportJob($this->candidate->id);
        
        $this->assertInstanceOf(ShouldQueue::class, $job);
    }
}
