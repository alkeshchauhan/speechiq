<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadAloudResult extends Model
{
    protected $table = 'read_aloud_results';

    protected $fillable = [
        'audio_recording_id',
        'transcript',
        'pronunciation_score',
        'fluency_score',
        'accuracy_score',
        'wpm',
        'pause_count',
        'pause_duration',
        'missing_words',
        'extra_words',
        'accent',
        'overall_score',
        'correct_words',
        'similarity_percentage',
        'confidence_score',
        'speech_rate',
        'long_pauses',
    ];

    protected $casts = [
        'missing_words' => 'array',
        'extra_words' => 'array',
        'correct_words' => 'array',
        'pause_duration' => 'float',
        'similarity_percentage' => 'float',
        'confidence_score' => 'integer',
        'speech_rate' => 'float',
        'long_pauses' => 'integer',
    ];

    /**
     * Get the audio recording associated with this result.
     */
    public function audioRecording(): BelongsTo
    {
        return $this->belongsTo(AudioRecording::class, 'audio_recording_id');
    }
}
