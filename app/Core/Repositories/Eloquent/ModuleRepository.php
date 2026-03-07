<?php

namespace App\Core\Repositories\Eloquent;

use App\Core\DTO\ModuleDTO;
use App\Core\Models\Module;
use App\Core\Repositories\Interfaces\ModuleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Core\Helpers\ModelHelpers;

class ModuleRepository implements ModuleRepositoryInterface
{
    public function store(ModuleDTO $data): Module {
        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        return Module::create($arrayData);
    }

    public function update(Module $module, ModuleDTO $data): ?Module {
        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        $module->update($arrayData);

        return $module->fresh();
    }

    public function delete(Module $module): bool {
        return (bool) $module->delete();
    }

    public function list(array $params = []): LengthAwarePaginator {
        $query = Module::query();

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