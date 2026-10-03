<?php

namespace App\Modules\Security\Services;

use App\Core\Models\Resource;
use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Core\Services\ResourceService;
use App\Modules\Security\Repositories\Interfaces\PermissionRepositoryInterface;

class PermissionService extends BaseCrudService
{
    public function __construct(PermissionRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }

    protected function prepareData(mixed $data): mixed {
        $resourceService = app(ResourceService::class);
        $resource = $resourceService->find($data->resource_id)->data;

        $data->name = trim($resource->slug) . '.' . trim($data->action);

        return $data;
    }
}