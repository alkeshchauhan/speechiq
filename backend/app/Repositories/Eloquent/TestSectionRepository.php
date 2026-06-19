<?php

namespace App\Repositories\Eloquent;

use App\Models\TestSection;
use App\Repositories\Contracts\TestSectionRepositoryInterface;

class TestSectionRepository extends BaseRepository implements TestSectionRepositoryInterface
{
    public function __construct(TestSection $model)
    {
        parent::__construct($model);
    }
}
