<?php

namespace App\Modules\Security\Repositories\Eloquent;

use App\Modules\Security\DTO\PermissionDTO;
use App\Modules\Security\Models\Permission;
use App\Modules\Security\Repositories\Interfaces\PermissionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Core\Helpers\ModelHelpers;
use Illuminate\Support\Collection;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function store(PermissionDTO $data): Permission {
        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        return Permission::create($arrayData);
    }

    public function update(int $id, PermissionDTO $data): ?Permission {
        $permission = $this->findById($id);

        if (!$permission) {
            return null;
        }

        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        $permission->update($arrayData);

        return $permission->fresh();
    }

    public function delete(int $id): bool {
        $permission = $this->findById($id);

        if (!$permission) {
            return false;
        }

        return (bool) $permission->delete();
    }

    public function findById(int $id): ?Permission {
        return Permission::find($id);
    }

    public function list(array $filters = []): LengthAwarePaginator {
        $query = Permission::query();

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

    public function findByResourceAndAction(string $resource, string $action): ?Permission
    {
        return Permission::where('resource', $resource)
                        ->where('action', $action)
                        ->first();
    }
}