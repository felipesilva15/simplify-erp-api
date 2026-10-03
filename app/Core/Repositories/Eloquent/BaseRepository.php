<?php

namespace App\Core\Repositories\Eloquent;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Core\Helpers\ModelHelpers;
use App\Core\Repositories\Interfaces\BaseRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct() {
        $this->model = app($this->getModelClass());
    }

    abstract protected function getModelClass(): string;

    /**
     * Colunas usadas pela busca textual (`q`) das listagens. Os tipos são resolvidos
     * por introspecção do schema, então o hook devolve apenas os nomes das colunas.
     */
    protected function getListColumnsToFilter(): array {
        return [
            'id'
        ];
    }

    /**
     * Colunas usadas pela busca textual (`q`) do lookup.Os tipos são resolvidos por
     * introspecção do schema, então o hook devolve apenas os nomes das colunas.
     */
    protected function getLookupColumnsToFilter(): array {
        return [
            'id'
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

    protected function withRelations(): array {
        return [];
    }

    public function list(array $params = []): LengthAwarePaginator {
        $query = $this->applyListParams($this->model::query(), $params);

        $perPage = isset($params['per_page']) ? (int) $params['per_page'] : 15;
        $page = isset($params['page']) ? (int) $params['page'] : 1;

        if ($relations = $this->withRelations()) {
            $query = $query->with($relations);
        }

        return $query->paginate(perPage: $perPage, page: $page)
            ->withQueryString();
    }

    public function getExportQuery(array $params = []): Builder {
        $hasExplicitSorts = !empty($params['sorts']);

        $query = $this->applyListParams($this->model::query(), $params);

        if ($hasExplicitSorts) {
            // Ordenação estável para garantir consistência na exportação em chunks.
            $query->orderBy($this->model->getKeyName());
        }

        return $query;
    }

    private function applyListParams(Builder $query, array $params): Builder {
        $columns = $this->resolveColumns($query, $params);

        $query = $this->applySearch($query, $params['q'] ?? null, $this->getListColumnsToFilter(), $columns);
        $query = $this->applyFilters($query, $params['filters'] ?? [], $columns);
        $query = $this->applySorts($query, $params['sorts'] ?? null, $columns);

        return $query;
    }

    /**
     * Introspecta o schema uma única vez por requisição, somente quando algum dos
     * parâmetros precisa da lista de colunas (busca, filtro ou ordenação).
     */
    private function resolveColumns(Builder $query, array $params): ?Collection {
        if (
            (empty($params['q']) && empty($params['filters']) && empty($params['sorts']))
        ) {
            return null;
        }

        return ModelHelpers::getColumnsCollection($query);
    }

    private function getSearchOptions(?Collection $columns = null): array {
        return [
            'columns' => $columns,
            'masked_columns' => $this->getMaskedSearchableColumns(),
            'not_normalized_columns' => $this->getNonNormalizedSearchableColumns(),
        ];
    }

    private function applySearch(Builder $query, mixed $term, array $columnsToSearch, ?Collection $columns = null): Builder {
        return ModelHelpers::setSearchOnQuery($query, $term, $columnsToSearch, $this->getSearchOptions($columns));
    }

    private function applyFilters(Builder $query, array $filters, ?Collection $columns = null): Builder {
        if (empty($filters)) {
            return $query;
        }

        return ModelHelpers::setFiltersOnQuery($query, $filters, [], $this->getSearchOptions($columns));
    }

    private function applySorts(Builder $query, mixed $sorts, ?Collection $columns = null): Builder {
        $sorts = empty($sorts) ? ModelHelpers::DEFAULT_SORTS : (string) $sorts;

        return ModelHelpers::setSortsOnQuery($query, $sorts, $this->getSearchOptions($columns));
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

        $columns = $this->resolveColumns($query, $params);

        $query = $this->applySearch($query, $params['q'] ?? null, $this->getLookupColumnsToFilter(), $columns);
        $query = $this->applySorts($query, $params['sorts'] ?? null, $columns);

        if (isset($params['keys']) && count($params['keys']))
            $query->whereIn($this->getLookupKeyColumn(), $params['keys']);

        if ($relations = $this->withRelations())
            $query = $query->with($relations);

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
