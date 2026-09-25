<?php

namespace App\Core\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Core\Repositories\Interfaces\CityRepositoryInterface;

class CityService extends BaseCrudService
{
    public function __construct(CityRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }
}