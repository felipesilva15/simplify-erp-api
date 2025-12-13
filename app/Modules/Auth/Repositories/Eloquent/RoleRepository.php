<?php

namespace App\Modules\Auth\Repositories\Eloquent;

use App\Modules\Auth\DTO\RoleDTO;
use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Core\Helpers\ModelHelpers;

class RoleRepository implements RoleRepositoryInterface
{
    public function store(RoleDTO $data): Role {
        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        return Role::create($arrayData);
    }

    public function update(int $id, RoleDTO $data): ?Role {
        $role = $this->findById($id);

        if (!$role) {
            return null;
        }

        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        $role->update($arrayData);

        return $role->fresh();
    }

    public function delete(int $id): bool {
        $role = $this->findById($id);

        if (!$role) {
            return false;
        }

        return (bool) $role->delete();
    }

    public function findById(int $id): ?Role {
        return Role::find($id);
    }

    public function list(array $filters = []): LengthAwarePaginator {
        $query = Role::query();

        // Filtering
        if (count($filters) > 0)
            $query = ModelHelpers::setFiltersOnQuery($query, $filters);

        // Sorting
        if (count($filters['sort_by'] ?? []) > 0 && count($filters['sort_dir'] ?? []) > 0)
            $query = ModelHelpers::setSortsOnQuery($query, $filters['sort_by'], $filters['sort_dir']);

        // Pagination
        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;
        $page = isset($filters['page']) ? (int) $filters['page'] : 1;

        return $query->paginate(perPage: $perPage, page: $page)->withQueryString();
    }

    public function syncPermissions(int $id, array $permissionIds = []): ?Role {
        $role = $this->findById($id);

        if (!$role) {
            return null;
        }
        
        $role->permissions()->sync($permissionIds);

        return $role->fresh();
    }
}