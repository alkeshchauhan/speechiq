<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Test;
use App\Models\Question;
use App\Models\AudioRecording;
use App\Models\ReadAloudResult;
use App\Models\InterviewResult;
use App\Models\AnalysisReport;
use App\Jobs\GenerateReportJob;
use App\Services\AnalysisReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $candidate;
    protected Question $readAloudQuestion;
    protected Question $interviewQuestion;

    protected function setUp(): void
    {
        parent::setUp();

        // Register default user role
        $role = Role::create(['name' => 'User', 'slug' => 'user']);
        $this->candidate = User::factory()->create();
        $this->candidate->roles()->attach($role->id);

        // Seeding configurations settings
        \DB::table('settings')->insert([
            ['setting_key' => 'AI_API_URL', 'setting_value' => 'http://127.0.0.1:8000', 'setting_type' => 'text', 'is_encrypted' => false],
        ]);

        // Create standard tests
        $test1 = Test::create(['title' => 'Read Aloud test', 'type' => 'READ_ALOUD', 'is_active' => true]);
        $test2 = Test::create(['title' => 'AI Interview test', 'type' => 'AI_INTERVIEW', 'is_active' => true]);

        $this->readAloudQuestion = Question::create([
            'test_id' => $test1->id,
            'question_text' => 'Read paragraph.',
            'question_type' => 'READ_ALOUD',
            'sort_order' => 1
        ]);

        $this->interviewQuestion = Question::create([
            'test_id' => $test2->id,
            'question_text' => 'HR Prompt context.',
            'question_type' => 'AI_INTERVIEW',
            'sort_order' => 1
        ]);
    }

    /**
     * Test Report Service calculates average scores accurately.
     */
    public function test_report_service_calculates_correct_averages(): void
    {
        // 1. Create a Completed Read Aloud Recording & Result
        $rec1 = AudioRecording::create([
            'user_id' => $this->candidate->id,
            'question_id' => $this->readAloudQuestion->id,
            'audio_path' => 'recordings/r1.webm',
            'duration' => 5.0,
            'status' => 'completed'
        ]);
        ReadAloudResult::create([
            'audio_recording_id' => $rec1->id,
            'transcript' => 'read paragraph text',
            'pronunciation_score' => 80,
            'fluency_score' => 80,
            'accuracy_score' => 80,
            'wpm' => 120,
            'pause_count' => 1,
            'pause_duration' => 1.0,
            'accent' => 'UK Accent',
            'overall_score' => 80
        ]);

        // 2. Create a Completed AI Interview Recording & Result
        $rec2 = AudioRecording::create([
            'user_id' => $this->candidate->id,
            'question_id' => $this->interviewQuestion->id,
            'audio_path' => 'recordings/i1.webm',
            'duration' => 10.0,
            'status' => 'completed'
        ]);
        InterviewResult::create([
            'audio_recording_id' => $rec2->id,
            'question' => 'HR Prompt context.',
            'transcript' => 'I prefer Laravel frameworks',
            'grammar_score' => 90,
            'vocabulary_score' => 90,
            'content_score' => 90,
            'confidence_score' => 90,
            'pronunciation_score' => 90,
            'fluency_score' => 90,
            'accent' => 'UK Accent',
            'overall_score' => 90
        ]);

        $service = resolve(AnalysisReportService::class);
        $report = $service->generateUserReport($this->candidate->id);

        $this->assertEquals(85, $report->overall_score); // Average of (80 + 90)
        $this->assertEquals(80, $report->read_aloud_average);
        $this->assertEquals(90, $report->interview_average);
        $this->assertEquals(2, $report->total_tests_taken);

        // Check if progress data contains timeline coordinates
        $this->assertCount(2, $report->progress_data);
    }

    /**
     * Test Report Dashboard view route.
     */
    public function test_reports_dashboard_renders_successfully(): void
    {
        $response = $this->actingAs($this->candidate)->get(route('practice.reports.index'));

        $response->assertStatus(200);
        $response->assertViewHas('report');
    }

    /**
     * Test print download report view route.
     */
    public function test_print_report_download_view(): void
    {
        $report = AnalysisReport::create([
            'user_id' => $this->candidate->id,
            'overall_score' => 85,
            'read_aloud_average' => 80,
            'interview_average' => 90,
            'total_tests_taken' => 2,
            'progress_data' => [],
            'improvement_areas' => []
        ]);

        $response = $this->actingAs($this->candidate)->get(route('practice.reports.download', $report->id));

        $response->assertStatus(200);
        $response->assertSee('SpeechIQ Assessment Report');
    }

    /**
     * Test queue GenerateReportJob compiles cleanly.
     */
    public function test_job_updates_report_correctly(): void
    {
        $rec = AudioRecording::create([
            'user_id' => $this->candidate->id,
            'question_id' => $this->readAloudQuestion->id,
            'audio_path' => 'recordings/r1.webm',
            'duration' => 5.0,
            'status' => 'completed'
        ]);
        ReadAloudResult::create([
            'audio_recording_id' => $rec->id,
            'transcript' => 'read paragraph',
            'overall_score' => 88
        ]);

        GenerateReportJob::dispatch($this->candidate->id);

        $this->assertDatabaseHas('analysis_reports', [
            'user_id' => $this->candidate->id,
            'overall_score' => 88,
            'total_tests_taken' => 1
        ]);
    }

    /**
     * Test admin can access admin reports dashboard list.
     */
    public function test_admin_can_access_reports_dashboard(): void
    {
        $adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $adminUser = User::factory()->create();
        $adminUser->roles()->attach($adminRole->id);

        $response = $this->actingAs($adminUser)->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertViewHas('reports');
        $response->assertViewHas('totalReports');
    }

    /**
     * Test regular candidate gets blocked from admin reports.
     */
    public function test_candidate_cannot_access_admin_reports_dashboard(): void
    {
        $response = $this->actingAs($this->candidate)->get(route('admin.reports.index'));

        $response->assertStatus(403);
    }

    /**
     * Test admin can inspect and download any candidate report sheet.
     */
    public function test_admin_can_download_any_candidate_report(): void
    {
        $adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $adminUser = User::factory()->create();
        $adminUser->roles()->attach($adminRole->id);

        $report = AnalysisReport::create([
            'user_id' => $this->candidate->id,
            'overall_score' => 85,
            'read_aloud_average' => 80,
            'interview_average' => 90,
            'total_tests_taken' => 2,
            'progress_data' => [],
            'improvement_areas' => []
        ]);

        $response = $this->actingAs($adminUser)->get(route('practice.reports.download', $report->id));

        $response->assertStatus(200);
        $response->assertSee('SpeechIQ Assessment Report');
    }
}
