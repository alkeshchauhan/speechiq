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
        'progress_data',
        'improvement_areas',
        'pdf_path',
    ];

    protected $casts = [
        'progress_data' => 'array',
        'improvement_areas' => 'array',
    ];

    /**
     * Get the user that owns this analysis report.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
