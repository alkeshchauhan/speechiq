<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AudioRecording extends Model
{
    protected $fillable = [
        'user_id',
        'question_id',
        'audio_path',
        'duration',
        'status',
    ];

    /**
     * Get the candidate user that owns the recording.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the question associated with the recording.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the read aloud result associated with the recording.
     */
    public function readAloudResult(): HasOne
    {
        return $this->hasOne(ReadAloudResult::class, 'audio_recording_id');
    }

    /**
     * Get the AI interview result associated with the recording.
     */
    public function interviewResult(): HasOne
    {
        return $this->hasOne(InterviewResult::class, 'audio_recording_id');
    }
}
