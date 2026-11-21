<?php

namespace App\Modules\Construction\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Construction\Repositories\Interfaces\ProjectTypeRepositoryInterface;

class ProjectTypeService extends BaseCrudService
{
    public function __construct(ProjectTypeRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }
}