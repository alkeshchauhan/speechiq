<?php

namespace App\Services;

use App\Repositories\Contracts\QuestionRepositoryInterface;

class QuestionService extends BaseService
{
    protected QuestionRepositoryInterface $questionRepository;

    public function __construct(QuestionRepositoryInterface $questionRepository)
    {
        parent::__construct($questionRepository);
        $this->questionRepository = $questionRepository;
    }
}
