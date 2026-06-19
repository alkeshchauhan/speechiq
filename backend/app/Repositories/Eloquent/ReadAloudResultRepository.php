<?php

namespace App\Repositories\Eloquent;

use App\Models\ReadAloudResult;
use App\Repositories\Contracts\ReadAloudResultRepositoryInterface;

class ReadAloudResultRepository extends BaseRepository implements ReadAloudResultRepositoryInterface
{
    public function __construct(ReadAloudResult $model)
    {
        parent::__construct($model);
    }
}
