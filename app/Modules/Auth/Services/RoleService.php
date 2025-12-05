<?php

namespace App\Modules\Auth\Services;

use App\Core\Exceptions\NotFoundHttpException;
use App\Modules\Auth\DTO\RoleDTO;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleService
{
    public function __construct(protected RoleRepositoryInterface $repository) { }

    public function store(RoleDTO $data): Role {
        return $this->repository->store($data);
    }

    public function edit(int $id): ?Role {
        $role = $this->repository->findById($id);

        if (!$role) {
            throw new NotFoundHttpException();
        }

        return $role;
    }

    public function update(int $id, RoleDTO $data): ?Role {
        $role = $this->repository->update($id, $data);

        if (!$role) {
            throw new NotFoundHttpException();
        }

        return $role;
    }

    public function delete(int $id): bool {
        return $this->repository->delete($id);
    }

    public function findById(int $id): ?Role {
        $role = $this->repository->findById($id);

        if (!$role) {
            throw new NotFoundHttpException();
        }

        return $role;
    }

    public function list(array $filters = []): LengthAwarePaginator {
        return $this->repository->list($filters);
    }
}