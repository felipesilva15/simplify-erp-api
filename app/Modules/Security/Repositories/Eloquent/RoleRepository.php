<?php

namespace App\Modules\Security\Repositories\Eloquent;

use App\Modules\Security\DTO\RoleDTO;
use App\Modules\Security\Models\Role;
use App\Modules\Security\Repositories\Interfaces\RoleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Core\Helpers\ModelHelpers;
use Illuminate\Database\Eloquent\Builder;

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

    public function list(array $params = []): LengthAwarePaginator {
        $query = Role::query();

        // Filtering
        if (isset($params['filters']) && count($params['filters']) > 0)
            $query = ModelHelpers::setFiltersOnQuery($query, $params['filters']);

        // Sorting
        if (!empty($params['sorts']))
            $query = ModelHelpers::setSortsOnQuery($query, $params['sorts']);

        // Pagination
        $perPage = isset($params['per_page']) ? (int) $params['per_page'] : 15;
        $page = isset($params['page']) ? (int) $params['page'] : 1;

        return $query->paginate(perPage: $perPage, page: $page)->withQueryString();
    }

    public function syncPermissions(Role $role, array $permissionIds = []): ?Role {
        $role->permissions()->sync($permissionIds);
        return $role->fresh();
    }

    public function lookup(array $params = []): LengthAwarePaginator {
        $query = Role::query();

        if (isset($params['q']) && $params['q'] != '') {
            $query->where(function (Builder $query) use ($params) {
                return $query->orWhere('name', 'like', "%".$params['q']."%")
                            ->orWhere('id', '=', (int) $params['q']);
            });
        }

        $perPage = isset($params['per_page']) ? (int) $params['per_page'] : 30;
        $page = isset($params['page']) ? (int) $params['page'] : 1;

        return $query->without('permissions')->paginate(perPage: $perPage, page: $page)->withQueryString();
    }
}