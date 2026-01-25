<?php

namespace App\Modules\Security\Repositories\Eloquent;

use App\Modules\Security\DTO\RoleDTO;
use App\Modules\Security\Models\Role;
use App\Modules\Security\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Core\Helpers\ModelHelpers;

class RoleRepository implements RoleRepositoryInterface
{
    public function store(RoleDTO $data): Role {
        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        return Role::create($arrayData);
    }

    public function update(Role $role, RoleDTO $data): ?Role {
        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        $role->update($arrayData);

        return $role->fresh();
    }

    public function delete(Role $role): bool {
        return (bool) $role->delete();
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

    public function syncPermissions(Role $role, array $permissionIds = []): ?Role {
        $role->permissions()->sync($permissionIds);
        return $role->fresh();
    }
}