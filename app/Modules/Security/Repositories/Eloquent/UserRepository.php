<?php

namespace App\Modules\Security\Repositories\Eloquent;

use App\Modules\Security\DTO\UserDTO;
use App\Modules\Security\Models\User;
use App\Modules\Security\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Core\Helpers\ModelHelpers;

class UserRepository implements UserRepositoryInterface
{
    public function store(UserDTO $data): User {
        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        return User::create($arrayData);
    }

    public function update(User $user, UserDTO $data): ?User {
        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        $user->update($arrayData);

        return $user->fresh();
    }

    public function delete(User $user): bool {
        return (bool) $user->delete();
    }

    public function list(array $filters = []): LengthAwarePaginator {
        $query = User::query();

        // Filtering
        if (count($filters) > 0)
            $query = ModelHelpers::setFiltersOnQuery($query, $filters);

        // Sorting
        if (!empty($filters['sorts']))
            $query = ModelHelpers::setSortsOnQuery($query, $filters['sorts']);

        // Pagination
        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;
        $page = isset($filters['page']) ? (int) $filters['page'] : 1;

        return $query->paginate(perPage: $perPage, page: $page)->withQueryString();
    }

    public function syncRoles(User $user, array $roleIds = []): ?User {
        $user->roles()->sync($roleIds);

        return $user->fresh();
    }
}