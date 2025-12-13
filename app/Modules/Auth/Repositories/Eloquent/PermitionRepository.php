<?php

namespace App\Modules\Auth\Repositories\Eloquent;

use App\Modules\Auth\DTO\PermitionDTO;
use App\Modules\Auth\Models\Permition;
use App\Modules\Auth\Repositories\Interfaces\PermitionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Core\Helpers\ModelHelpers;
use Illuminate\Support\Collection;

class PermitionRepository implements PermitionRepositoryInterface
{
    public function store(PermitionDTO $data): Permition {
        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        return Permition::create($arrayData);
    }

    public function update(int $id, PermitionDTO $data): ?Permition {
        $permition = $this->findById($id);

        if (!$permition) {
            return null;
        }

        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        $permition->update($arrayData);

        return $permition->fresh();
    }

    public function delete(int $id): bool {
        $permition = $this->findById($id);

        if (!$permition) {
            return false;
        }

        return (bool) $permition->delete();
    }

    public function findById(int $id): ?Permition {
        return Permition::find($id);
    }

    public function list(array $filters = []): LengthAwarePaginator {
        $query = Permition::query();

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

    public function findByGroupAndAction(string $group, string $action): ?Permition
    {
        return Permition::where('group', $group)
                        ->where('action', $action)
                        ->first();
    }
}