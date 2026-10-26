<?php

namespace App\Modules\Integration\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Integration\Repositories\IntegrationConnectionRepository;

class IntegrationConnectionService extends BaseCrudService
{
    
    public function __construct(IntegrationConnectionRepository $repository, ActivityLogService $activity)
    {
        $this->repository = $repository;
        $this->activity = $activity;
    }
}
