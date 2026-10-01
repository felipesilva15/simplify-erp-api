<?php

namespace App\Modules\Geography\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Geography\Repositories\Interfaces\StateRepositoryInterface;

class StateService extends BaseCrudService
{
    public function __construct(StateRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }
}