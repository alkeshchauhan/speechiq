<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AudioRecording;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AudioRecordingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a user can upload a simulated voice recording file.
     */
    public function test_user_can_upload_recording(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('recording.webm', 100, 'audio/webm');

        $response = $this->actingAs($user)->postJson(route('audio-recordings.store'), [
            'audio_file' => $file,
            'duration' => 12.5,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'audio_path',
                    'audio_url',
                    'duration',
                    'status',
                    'created_at',
                ]
            ]);

        $this->assertDatabaseHas('audio_recordings', [
            'user_id' => $user->id,
            'duration' => 12.5,
            'status' => 'pending',
        ]);

        $recording = AudioRecording::first();
        Storage::disk('public')->assertExists($recording->audio_path);
    }

    /**
     * Test validation checks fail for missing audio files.
     */
    public function test_upload_requires_valid_audio_file(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('audio-recordings.store'), [
            'duration' => 10,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['audio_file']);
    }
}
