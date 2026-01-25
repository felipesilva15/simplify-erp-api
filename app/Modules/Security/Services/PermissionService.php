<?php

namespace App\Modules\Security\Services;

use App\Core\DTO\ServiceResult;
use App\Modules\Security\DTO\PermissionDTO;
use App\Modules\Security\Models\Permission;
use App\Modules\Security\Repositories\Interfaces\PermissionRepositoryInterface;

class PermissionService
{
    public function __construct(protected PermissionRepositoryInterface $repository) { }

    public function store(PermissionDTO $data): ServiceResult {
        $permission = $this->repository->store($data);

        return new ServiceResult(
            data: $permission
        );
    }

    public function edit(Permission $permission): ServiceResult {
        return new ServiceResult(
            data: $permission,
            meta: [
                'editable' => true
            ]
        );
    }

    public function update(Permission $permission, PermissionDTO $data): ServiceResult {
        $permission = $this->repository->update($permission, $data);

        return new ServiceResult(
            data: $permission
        );
    }

    public function delete(Permission $permission): ServiceResult {
        return new ServiceResult(
            data: null,
            meta: [
                'deleted' => $this->repository->delete($permission)
            ]
        );
    }

    public function list(array $filters = []): ServiceResult {
        return new ServiceResult(
            data: $this->repository->list($filters)
        );
    }

    public function show(Permission $permission): ServiceResult {
        return new ServiceResult(
            data: $permission
        );
    }
}