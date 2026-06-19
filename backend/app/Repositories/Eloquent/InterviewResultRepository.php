<?php

namespace App\Repositories\Eloquent;

use App\Models\InterviewResult;
use App\Repositories\Contracts\InterviewResultRepositoryInterface;

class InterviewResultRepository extends BaseRepository implements InterviewResultRepositoryInterface
{
    public function __construct(InterviewResult $model)
    {
        parent::__construct($model);
    }
}
