<?php

namespace App\Modules\HR\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\HR\Repositories\Interfaces\ProfessionRepositoryInterface;

class ProfessionService extends BaseCrudService
{
    public function __construct(ProfessionRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }
}