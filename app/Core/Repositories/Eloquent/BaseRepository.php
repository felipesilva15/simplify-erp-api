<?php

namespace App\Core\Repositories\Eloquent;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Core\Enums\SqlQueryOperatorsEnum;
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

    protected function getLookupKeyColumn(): string {
        return 'id';
    }

    protected function getMaskedSearchableColumns(): array {
        return [];
    }

    protected function getNonNormalizedSearchableColumns(): array {
        return [];
    }

    public function list(array $params = []): LengthAwarePaginator {
        $query = $this->model::query();

        if (isset($params['filters']) && count($params['filters']) > 0)
            $query = ModelHelpers::setFiltersOnQuery(
                $query,
                $params['filters'],
                [],
                [
                    'masked_columns' => $this->getMaskedSearchableColumns(),
                    'not_normalized_columns' => $this->getNonNormalizedSearchableColumns(),
                ]
            );

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
            $isPgsql = ModelHelpers::isPgsql($query);

            $query->where(function (Builder $query) use ($filter, $isPgsql) {
                $maskedColumns = $this->getMaskedSearchableColumns();

                foreach ($this->getLookupColumnsToFilter() as $columnName => $type) {
                    match ($type) {
                        'string' => $isPgsql
                            ? $this->addPgsqlStringLookupFilter($query, $columnName, $filter, $maskedColumns)
                            : $query->orWhere($columnName, 'like', "%".trim($filter)."%"),
                        'int' => $query->orWhere($columnName, '=', (int) $filter),
                        default => $query->orWhere($columnName, '=', $filter),
                    };
                }

                return $query;
            });
        }

        if (isset($params['keys']) && count($params['keys']))
            $query->whereIn($this->getLookupKeyColumn(), $params['keys']);

        $perPage = isset($params['per_page']) ? (int) $params['per_page'] : 30;
        $page = isset($params['page']) ? (int) $params['page'] : 1;

        return $query->paginate(perPage: $perPage, page: $page)->withQueryString();
    }

    /**
     * Aplica um filtro de texto (LIKE) em uma coluna string no PostgreSQL, de forma
     * case-insensitive, unaccent-insensitive e removendo a máscara quando aplicável.
     */
    private function addPgsqlStringLookupFilter(Builder $query, string $columnName, string $filter, array $maskedColumns): void {
        $isMasked = in_array($columnName, $maskedColumns, true);

        ModelHelpers::addStringSearchToWhere(
            $query,
            $columnName,
            SqlQueryOperatorsEnum::Like->value,
            trim($filter),
            $isMasked,
            'or',
        );
    }

    public function sync(Model $entity, string $relationMethodName, array $ids = []): ?Model {
        if (!method_exists($entity, $relationMethodName)) {
            throw new Exception("$relationMethodName method doesn't exists on model {$this->getModelClass()}.   ");
        }

        $entity->$relationMethodName()->sync($ids);
        return $entity->fresh();
    }
}
