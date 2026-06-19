<?php

namespace App\Repositories\Eloquent;

use App\Models\AudioRecording;
use App\Repositories\Contracts\AudioRecordingRepositoryInterface;

class AudioRecordingRepository extends BaseRepository implements AudioRecordingRepositoryInterface
{
    public function __construct(AudioRecording $model)
    {
        parent::__construct($model);
    }
}
