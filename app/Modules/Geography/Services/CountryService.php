<?php

namespace App\Modules\Geography\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Geography\Repositories\Interfaces\CountryRepositoryInterface;

class CountryService extends BaseCrudService
{
    public function __construct(CountryRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }
}