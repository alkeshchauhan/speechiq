<?php

namespace App\Services;

use App\Repositories\Contracts\TestSectionRepositoryInterface;

class TestSectionService extends BaseService
{
    protected TestSectionRepositoryInterface $testSectionRepository;

    public function __construct(TestSectionRepositoryInterface $testSectionRepository)
    {
        parent::__construct($testSectionRepository);
        $this->testSectionRepository = $testSectionRepository;
    }
}
