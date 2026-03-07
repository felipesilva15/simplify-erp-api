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

    public function update(Permission $permission, PermissionDTO $data): ?Permission {
        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        $permission->update($arrayData);

        return $permission->fresh();
    }

    public function delete(Permission $permission): bool {
        return (bool) $permission->delete();
    }

    public function list(array $params = []): LengthAwarePaginator {
        $query = Permission::query();

        // Filtering
        if (isset($params['filters']) && count($params['filters']) > 0)
            $query = ModelHelpers::setFiltersOnQuery($query, $params['filters']);

        // Sorting
        if (!empty($params['sorts']))
            $query = ModelHelpers::setSortsOnQuery($query, $params['sorts']);

        // Pagination
        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;
        $page = isset($filters['page']) ? (int) $filters['page'] : 1;

        return $query->paginate(perPage: $perPage, page: $page)->withQueryString();
    }
}