<?php

namespace App\Core\Repositories\Eloquent;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Core\Helpers\ModelHelpers;
use App\Core\Repositories\Interfaces\BaseRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct() {
        $this->model = app($this->getModelClass());
    }

    abstract protected function getModelClass(): string;

    protected function getLookupColumnsToFilter(): array {
        return [
            'id' => 'int'
        ];
    }

    public function list(array $params = []): LengthAwarePaginator {
        $query = $this->model::query();

        if (isset($params['filters']) && count($params['filters']) > 0)
            $query = ModelHelpers::setFiltersOnQuery($query, $params['filters']);

        if (!empty($params['sorts']))
            $query = ModelHelpers::setSortsOnQuery($query, $params['sorts']);

        $perPage = isset($params['per_page']) ? (int) $params['per_page'] : 15;
        $page = isset($params['page']) ? (int) $params['page'] : 1;

        return $query->paginate(perPage: $perPage, page: $page)->withQueryString();
    }

    public function getById(mixed $id): ?Model {
        return $this->model::find($id);
    }

    public function store(mixed $data): Model {
        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        return $this->model::create($arrayData);
    }

    public function update(Model $entity, mixed $data): ?Model {
        $arrayData = $data->toArray();
        unset($arrayData["id"]);

        $entity->update($arrayData);

        return $entity->fresh();
    }

    public function delete(Model $entity): bool {
        return (bool) $entity->delete();
    }

    public function lookup(array $params = []): LengthAwarePaginator {
        $query = $this->model::query();

        if (isset($params['q']) && $params['q'] != '') {
            $filter = $params['q'];

            $query->where(function (Builder $query) use ($filter) {
                foreach ($this->getLookupColumnsToFilter() as $columnName => $type) {
                    match ($type) {
                        'string' => $query->orWhere($columnName, 'like', "%".trim($filter)."%"),
                        'int' => $query->orWhere($columnName, '=', (int) $filter),
                        default => $query->orWhere($columnName, '=', $filter),
                    };
                }

                return $query;
            });
        }

        if (isset($params['keys']) && count($params['keys']))
            $query->whereIn('id', $params['keys']);

        $perPage = isset($params['per_page']) ? (int) $params['per_page'] : 30;
        $page = isset($params['page']) ? (int) $params['page'] : 1;

        return $query->paginate(perPage: $perPage, page: $page)->withQueryString();
    }

    public function sync(Model $entity, string $relationMethodName, array $ids = []): ?Model {
        if (!method_exists($entity, $relationMethodName)) {
            throw new Exception("$relationMethodName method doesn't exists on model {$this->getModelClass()}.   ");
        }

        $entity->$relationMethodName()->sync($ids);
        return $entity->fresh();
    }
}
