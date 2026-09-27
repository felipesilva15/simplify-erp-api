<?php

namespace App\Modules\ThirdParty\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\ThirdParty\Repositories\Interfaces\PartnerTypeRepositoryInterface;

class PartnerTypeService extends BaseCrudService
{
    public function __construct(PartnerTypeRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }
}