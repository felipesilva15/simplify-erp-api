<?php

namespace App\Modules\Security\Services;

use App\Core\DTO\ServiceResult;
use App\Modules\Security\DTO\RoleDTO;
use App\Modules\Security\Models\Role;
use App\Modules\Security\Repositories\Interfaces\RoleRepositoryInterface;

class RoleService
{
    public function __construct(protected RoleRepositoryInterface $repository) { }

    public function store(RoleDTO $data): ServiceResult {
        $role = $this->repository->store($data);

        return new ServiceResult(
            data: $role
        );
    }

    public function edit(Role $role): ServiceResult {
        return new ServiceResult(
            data: $role,
            meta: [
                'editable' => true
            ]
        );
    }

    public function update(Role $role, RoleDTO $data): ServiceResult {
        $role = $this->repository->update($role, $data);

        return new ServiceResult(
            data: $role
        );
    }

    public function delete(Role $role): ServiceResult {
        return new ServiceResult(
            data: null,
            meta: [
                'deleted' => $this->repository->delete($role)
            ]
        );
    }

    public function list(array $filters = []): ServiceResult {
        return new ServiceResult(
            data: $this->repository->list($filters)
        );
    }

    public function show(Role $role): ServiceResult {
        return new ServiceResult(
            data: $role
        );
    }

    public function definePermissions(Role $role, array $permissionIds = []): ServiceResult {
        return new ServiceResult(
            data: $this->repository->syncPermissions($role, $permissionIds)
        );
    }
}