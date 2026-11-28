<?php

namespace App\Modules\Inventory\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Inventory\Repositories\Interfaces\WarehouseRepositoryInterface;

class WarehouseService extends BaseCrudService
{
    public function __construct(WarehouseRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }
}