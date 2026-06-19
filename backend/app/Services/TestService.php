<?php

namespace App\Services;

use App\Repositories\Contracts\TestRepositoryInterface;

class TestService extends BaseService
{
    protected TestRepositoryInterface $testRepository;

    public function __construct(TestRepositoryInterface $testRepository)
    {
        parent::__construct($testRepository);
        $this->testRepository = $testRepository;
    }
}
