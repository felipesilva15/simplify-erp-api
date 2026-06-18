<?php

namespace App\Modules\Security\Services;

use App\Core\Services\BaseCrudService;
use App\Modules\Security\Repositories\Interfaces\PermissionRepositoryInterface;

class PermissionService extends BaseCrudService
{
    public function __construct(PermissionRepositoryInterface $repository) {
        $this->repository = $repository;
    }
}