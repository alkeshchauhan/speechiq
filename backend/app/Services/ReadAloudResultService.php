<?php

namespace App\Services;

use App\Repositories\Contracts\ReadAloudResultRepositoryInterface;
use App\Models\ReadAloudResult;

class ReadAloudResultService extends BaseService
{
    protected ReadAloudResultRepositoryInterface $readAloudResultRepository;

    public function __construct(ReadAloudResultRepositoryInterface $readAloudResultRepository)
    {
        parent::__construct($readAloudResultRepository);
        $this->readAloudResultRepository = $readAloudResultRepository;
    }

    /**
     * Get a result by recording ID.
     */
    public function getByRecording(int $recordingId): ?ReadAloudResult
    {
        return $this->readAloudResultRepository->all()
            ->where('audio_recording_id', $recordingId)
            ->first();
    }
}
