<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewResult extends Model
{
    protected $fillable = [
        'audio_recording_id',
        'question',
        'transcript',
        'grammar_score',
        'vocabulary_score',
        'content_score',
        'communication_score',
        'confidence_score',
        'pronunciation_score',
        'fluency_score',
        'accent',
        'overall_score',
        'feedback',
        'tone',
        'wpm',
    ];

    protected $casts = [
        'grammar_score' => 'integer',
        'vocabulary_score' => 'integer',
        'content_score' => 'integer',
        'communication_score' => 'integer',
        'confidence_score' => 'integer',
        'pronunciation_score' => 'integer',
        'fluency_score' => 'integer',
        'overall_score' => 'integer',
        'wpm' => 'integer',
        'tone' => 'string',
    ];

    /**
     * Get the audio recording that belongs to this interview result.
     */
    public function audioRecording(): BelongsTo
    {
        return $this->belongsTo(AudioRecording::class);
    }
}
