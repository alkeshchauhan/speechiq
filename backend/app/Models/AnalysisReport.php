<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisReport extends Model
{
    protected $fillable = [
        'user_id',
        'overall_score',
        'read_aloud_average',
        'interview_average',
        'total_tests_taken',
        'primary_language',
        'primary_accent',
        'primary_tone',
        'confidence_average',
        'pronunciation_average',
        'fluency_average',
        'accuracy_average',
        'grammar_average',
        'vocabulary_average',
        'content_average',
        'communication_average',
        'wpm_average',
        'pause_count_average',
        'pause_duration_average',
        'progress_data',
        'improvement_areas',
        'pdf_path',
    ];

    protected $casts = [
        'progress_data' => 'array',
        'improvement_areas' => 'array',
        'pause_duration_average' => 'float',
    ];

    /**
     * Get the user that owns this analysis report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
