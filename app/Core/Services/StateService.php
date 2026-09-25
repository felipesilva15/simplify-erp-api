<?php

namespace App\Core\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Core\Repositories\Interfaces\StateRepositoryInterface;

class StateService extends BaseCrudService
{
    public function __construct(StateRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }
}