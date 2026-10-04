<?php

namespace App\Modules\Security\Services;

use App\Core\DTO\ServiceResult;
use App\Core\Services\BaseCrudService;
use App\Core\Services\ActivityLogService;
use App\Modules\Security\Repositories\Interfaces\ResourceRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Override;

class ResourceService extends BaseCrudService
{
    public function __construct(ResourceRepositoryInterface $repository, ActivityLogService $activity)
    {
        $this->repository = $repository;
        $this->activity = $activity;
    }
}
