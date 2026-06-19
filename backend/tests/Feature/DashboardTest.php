<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Test;
use App\Models\Setting;
use App\Models\AudioRecording;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $candidate;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Register default roles
        $userRole = Role::create(['name' => 'User', 'slug' => 'user']);
        $adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin']);

        // Create accounts
        $this->candidate = User::factory()->create();
        $this->candidate->roles()->attach($userRole->id);

        $this->admin = User::factory()->create();
        $this->admin->roles()->attach($adminRole->id);

        // Seeding database settings
        Setting::create([
            'setting_key' => 'AI_API_URL',
            'setting_value' => 'http://127.0.0.1:8000',
            'setting_type' => 'text',
            'is_encrypted' => false
        ]);
    }

    /**
     * Test candidate user can view their dashboard and see aggregates variables.
     */
    public function test_candidate_can_view_dynamic_dashboard(): void
    {
        // Add a completed practice recording
        AudioRecording::create([
            'user_id' => $this->candidate->id,
            'audio_path' => 'recordings/r1.webm',
            'duration' => 15.0,
            'status' => 'completed'
        ]);

        $response = $this->actingAs($this->candidate)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHasAll([
            'totalCompleted',
            'readAloudAvg',
            'interviewAvg',
            'report',
            'pastAttempts'
        ]);
        
        $response->assertSee('Read Aloud Practices');
        $response->assertSee('Conversational AI Interviews');
    }

    /**
     * Test admin user can view admin dashboard.
     */
    public function test_admin_can_view_dynamic_dashboard_statistics(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHasAll([
            'totalUsers',
            'activeTestsCount',
            'completedAnalysesCount',
            'activeSettingsCount'
        ]);
    }

    /**
     * Test regular candidate gets redirected from admin dashboard.
     */
    public function test_candidate_role_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->candidate)->get(route('admin.dashboard'));

        // Middleware redirects to user welcome or homepage (usually throws 403 or redirects)
        $response->assertStatus(403);
    }
}
