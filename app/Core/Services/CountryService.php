<?php

namespace App\Core\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Core\Repositories\Interfaces\CountryRepositoryInterface;

class CountryService extends BaseCrudService
{
    public function __construct(CountryRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }
}