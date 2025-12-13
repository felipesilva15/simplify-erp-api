<?php

namespace App\Modules\Auth\Services;

use App\Core\Exceptions\NotFoundHttpException;
use App\Modules\Auth\DTO\PermissionDTO;
use App\Modules\Auth\Models\Permission;
use App\Modules\Auth\Repositories\Interfaces\PermissionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PermissionService
{
    public function __construct(protected PermissionRepositoryInterface $repository) { }

    public function store(PermissionDTO $data): Permission {
        return $this->repository->store($data);
    }

    public function edit(int $id): ?Permission {
        $permission = $this->repository->findById($id);

        if (!$permission) {
            throw new NotFoundHttpException();
        }

        return $permission;
    }

    public function update(int $id, PermissionDTO $data): ?Permission {
        $permission = $this->repository->update($id, $data);

        if (!$permission) {
            throw new NotFoundHttpException();
        }

        return $permission;
    }

    public function delete(int $id): bool {
        return $this->repository->delete($id);
    }

    public function findById(int $id): ?Permission {
        $permission = $this->repository->findById($id);

        if (!$permission) {
            throw new NotFoundHttpException();
        }

        return $permission;
    }

    public function list(array $filters = []): LengthAwarePaginator {
        return $this->repository->list($filters);
    }
}