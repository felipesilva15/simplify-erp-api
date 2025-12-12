<?php

namespace App\Modules\Auth\Repositories\Eloquent;

use App\Modules\Auth\DTO\UserDTO;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Core\Helpers\ModelHelpers;

class UserRepository implements UserRepositoryInterface
{
    public function store(UserDTO $data): User {
        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        return User::create($arrayData);
    }

    public function update(int $id, UserDTO $data): ?User {
        $user = $this->findById($id);

        if (!$user) {
            return null;
        }

        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        $user->update($arrayData);

        return $user->fresh();
    }

    public function delete(int $id): bool {
        $user = $this->findById($id);

        if (!$user) {
            return false;
        }

        return (bool) $user->delete();
    }

    public function findById(int $id): ?User {
        return User::find($id);
    }

    public function list(array $filters = []): LengthAwarePaginator {
        $query = User::query();

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
}