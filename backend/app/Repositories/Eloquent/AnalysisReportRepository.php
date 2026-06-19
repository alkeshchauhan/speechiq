<?php

namespace App\Repositories\Eloquent;

use App\Models\AnalysisReport;
use App\Repositories\Contracts\AnalysisReportRepositoryInterface;

class AnalysisReportRepository extends BaseRepository implements AnalysisReportRepositoryInterface
{
    public function __construct(AnalysisReport $model)
    {
        parent::__construct($model);
    }
}
