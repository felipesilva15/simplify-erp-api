<?php

namespace App\Core\Services;

use App\Core\DTO\AttributesDTO;
use App\Core\DTO\ServiceResult;
use App\Core\Enums\ActivityActionEnum;
use App\Core\Exceptions\BusinessRuleException;
use App\Core\Repositories\Interfaces\BaseRepositoryInterface;
use App\Core\Services\Children\ChildRelation;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

abstract class BaseCrudService
{
    protected BaseRepositoryInterface $repository;
    protected ActivityLogService $activity;

    public function store(mixed $data): ServiceResult {
        $data = $this->prepareData($data);

        [$entity, $children] = DB::transaction(function () use ($data): array {
            $entity = $this->repository->store($this->extractHeaderData($data));

            $children = $this->syncChildren($entity, $data);

            $this->activity->log($entity, ActivityActionEnum::Created);

            return [$entity, $children];
        });

        return new ServiceResult(
            data: $this->loadChildren($entity),
            meta: $this->childrenMeta($children)
        );
    }

    public function edit(Model $entity): ServiceResult {
        return new ServiceResult(
            data: $this->loadChildren($entity),
            meta: [
                'editable' => true,
                'warnings' => []
            ]
        );
    }

    public function update(Model $entity, mixed $data): ServiceResult {
        $data = $this->prepareData($data);

        [$entity, $children] = DB::transaction(function () use ($entity, $data): array {
            $entity = $this->repository->update($entity, $this->extractHeaderData($data));

            $children = $this->syncChildren($entity, $data);

            $this->activity->log($entity, ActivityActionEnum::Updated);

            return [$entity, $children];
        });

        return new ServiceResult(
            data: $this->loadChildren($entity),
            meta: $this->childrenMeta($children)
        );
    }

    public function delete(Model $entity): ServiceResult {
        $deleted = $this->repository->delete($entity);
        $this->activity->log($entity, ActivityActionEnum::Deleted);

        return new ServiceResult(
            data: null,
            meta: [
                'deleted' => $deleted
            ]
        );
    }

    public function list(array $filters = []): ServiceResult {
        return new ServiceResult(
            data: $this->repository->list($filters)
        );
    }

    public function exportQuery(array $params = []): Builder {
        return $this->repository->getExportQuery($params);
    }

    public function show(Model $entity): ServiceResult {
        return new ServiceResult(
            data: $this->loadChildren($entity)
        );
    }

    public function find(mixed $id): ServiceResult {
        return new ServiceResult(
            data: $this->loadChildren($this->repository->getById($id))
        );
    }

    public function lookup(array $params): ServiceResult {
        return new ServiceResult(
            data: $this->repository->lookup($params)
        );
    }

    protected function prepareData(mixed $data): mixed {
        return $data;
    }

    protected function childRelations(): array {
        return [];
    }

    protected function extractHeaderData(mixed $data): mixed {
        $childKeys = array_keys($this->childRelations());

        if ($childKeys === [] || !is_object($data) || !method_exists($data, 'toArray')) {
            return $data;
        }

        $attributes = $data->toArray();

        foreach ($childKeys as $key) {
            unset($attributes[$key]);
        }

        return new AttributesDTO($attributes);
    }

    protected function syncChildren(Model $header, mixed $data): array {
        $results = [];

        foreach ($this->childRelations() as $key => $definition) {
            $items = $this->extractChildItems($data, $key);

            if ($items === null) {
                continue;
            }

            $results[$key] = $definition->resolve($header, $this->activity)->sync($items)->toArray();
        }

        return $results;
    }

    protected function loadChildren(?Model $entity, ?array $relations = null): ?Model {
        $relations ??= array_keys($this->childRelations());

        if ($entity === null || $relations === []) {
            return $entity;
        }

        return $entity->loadMissing($relations);
    }

    protected function extractChildItems(mixed $data, string $key): ?array {
        $value = match (true) {
            is_array($data)  => $data[$key] ?? null,
            is_object($data) => $data->{$key} ?? null,
            default          => null,
        };

        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        if ($value instanceof Arrayable) {
            return $value->toArray();
        }

        throw new BusinessRuleException(
            message: sprintf('Os itens de [%s] foram enviados em formato inválido.', $key),
            errors: [$key => 'Itens esperados no formato de lista.']
        );
    }

    private function childrenMeta(array $children): array {
        return $children === [] ? [] : ['children' => $children];
    }
}
