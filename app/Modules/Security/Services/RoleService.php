<?php

namespace App\Modules\Security\Services;

use App\Core\DTO\ServiceResult;
use App\Core\Enums\ActivityActionEnum;
use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Security\Models\Role;
use App\Modules\Security\Repositories\Interfaces\RoleRepositoryInterface;

class RoleService extends BaseCrudService
{
    public function __construct(RoleRepositoryInterface $repository, ActivityLogService $activity) {
        $this->repository = $repository;
        $this->activity = $activity;
    }

    public function definePermissions(Role $role, array $permissionIds = []): ServiceResult {
        $role = $this->repository->sync($role, 'permissions', $permissionIds);
        $this->activity->log($role, ActivityActionEnum::Updated, 'Atualizou as permissões do perfil');

        return new ServiceResult(
            data: $role
        );
    }
}