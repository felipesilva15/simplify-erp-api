<?php

namespace App\Modules\Security\Services;

use App\Core\DTO\ServiceResult;
use App\Core\Services\BaseCrudService;
use App\Modules\Security\Models\Role;
use App\Modules\Security\Repositories\Interfaces\RoleRepositoryInterface;

class RoleService extends BaseCrudService
{
    public function __construct(RoleRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function definePermissions(Role $role, array $permissionIds = []): ServiceResult {
        return new ServiceResult(
            data: $this->repository->sync($role, 'permissions', $permissionIds)
        );
    }
}