<?php

namespace App\Services;

use App\Repositories\Contracts\InterviewResultRepositoryInterface;
use App\Models\InterviewResult;

class InterviewResultService extends BaseService
{
    protected InterviewResultRepositoryInterface $interviewResultRepository;

    public function __construct(InterviewResultRepositoryInterface $interviewResultRepository)
    {
        parent::__construct($interviewResultRepository);
        $this->interviewResultRepository = $interviewResultRepository;
    }

    /**
     * Get an interview result by recording ID.
     */
    public function getByRecording(int $recordingId): ?InterviewResult
    {
        return $this->interviewResultRepository->all()
            ->where('audio_recording_id', $recordingId)
            ->first();
    }
}
