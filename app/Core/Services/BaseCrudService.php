<?php

namespace App\Core\Services;

use App\Core\DTO\AttributesDTO;
use App\Core\DTO\ServiceResult;
use App\Core\Enums\ActivityActionEnum;
use App\Core\Exceptions\BusinessRuleException;
use App\Core\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

abstract class BaseCrudService
{
    protected BaseRepositoryInterface $repository;

    protected ActivityLogService $activity;

    public function store(mixed $data): ServiceResult
    {
        $data = $this->prepareData($data);

        $this->beforeStore($data);

        [$entity, $children] = DB::transaction(function () use ($data): array {
            $entity = $this->repository->store($this->extractHeaderData($data));

            $children = $this->syncChildren($entity, $data);

            $entity = $this->afterStore($entity, $data);

            $this->activity->log($entity, ActivityActionEnum::Created);

            return [$entity, $children];
        });

        return new ServiceResult(
            data: $this->loadChildren($entity),
            meta: $this->childrenMeta($children)
        );
    }

    public function edit(Model $entity): ServiceResult
    {
        $warnings = $this->editWarnings($entity);

        return new ServiceResult(
            data: $this->loadChildren($entity),
            warnings: $warnings,
            meta: [
                'editable' => $this->canEdit($entity),
                'warnings' => $warnings,
            ]
        );
    }

    public function update(Model $entity, mixed $data): ServiceResult
    {
        $data = $this->prepareData($data);

        $this->beforeUpdate($entity, $data);

        [$entity, $children] = DB::transaction(function () use ($entity, $data): array {
            $entity = $this->repository->update($entity, $this->extractHeaderData($data));

            $children = $this->syncChildren($entity, $data);

            $entity = $this->afterUpdate($entity, $data);

            $this->activity->log($entity, ActivityActionEnum::Updated);

            return [$entity, $children];
        });

        return new ServiceResult(
            data: $this->loadChildren($entity),
            meta: $this->childrenMeta($children)
        );
    }

    public function delete(Model $entity): ServiceResult
    {
        $this->beforeDelete($entity);

        $deleted = DB::transaction(function () use ($entity): bool {
            $deleted = $this->repository->delete($entity);

            $this->afterDelete($entity);

            $this->activity->log($entity, ActivityActionEnum::Deleted);

            return $deleted;
        });

        return new ServiceResult(
            data: null,
            meta: [
                'deleted' => $deleted,
            ]
        );
    }

    public function list(array $filters = []): ServiceResult
    {
        return new ServiceResult(
            data: $this->repository->list($filters)
        );
    }

    public function exportQuery(array $params = []): Builder
    {
        return $this->repository->getExportQuery($params);
    }

    public function show(Model $entity): ServiceResult
    {
        return new ServiceResult(
            data: $this->loadChildren($entity)
        );
    }

    public function find(mixed $id): ServiceResult
    {
        return new ServiceResult(
            data: $this->loadChildren($this->repository->getById($id))
        );
    }

    public function lookup(array $params): ServiceResult
    {
        return new ServiceResult(
            data: $this->repository->lookup($params)
        );
    }

    protected function prepareData(mixed $data): mixed
    {
        return $data;
    }

    protected function beforeStore(mixed $data): void
    {
        // Sem regra de inclusão: cabe ao módulo sobrescrever.
    }

    protected function beforeUpdate(Model $entity, mixed $data): void
    {
        // Sem regra de alteração: cabe ao módulo sobrescrever.
    }

    protected function beforeDelete(Model $entity): void
    {
        // Sem regra de exclusão: cabe ao módulo sobrescrever.
    }

    protected function afterStore(Model $entity, mixed $data): Model
    {
        return $entity;
    }

    protected function afterUpdate(Model $entity, mixed $data): Model
    {
        return $entity;
    }

    protected function afterDelete(Model $entity): void
    {
        // Sem efeito colateral: cabe ao módulo sobrescrever.
    }

    protected function canEdit(Model $entity): bool
    {
        return true;
    }

    protected function editWarnings(Model $entity): array
    {
        return [];
    }

    protected function dataValue(mixed $data, string $key, mixed $default = null): mixed
    {
        return match (true) {
            is_array($data) => $data[$key] ?? $default,
            is_object($data) => $data->{$key} ?? $default,
            default => $default,
        };
    }

    protected function childRelations(): array
    {
        return [];
    }

    protected function extractHeaderData(mixed $data): mixed
    {
        $childKeys = array_keys($this->childRelations());

        if ($childKeys === [] || ! is_object($data) || ! method_exists($data, 'toArray')) {
            return $data;
        }

        $attributes = $data->toArray();

        foreach ($childKeys as $key) {
            unset($attributes[$key]);
        }

        return new AttributesDTO($attributes);
    }

    protected function syncChildren(Model $header, mixed $data): array
    {
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

    protected function loadChildren(?Model $entity, ?array $relations = null): ?Model
    {
        $relations ??= array_keys($this->childRelations());

        if ($entity === null || $relations === []) {
            return $entity;
        }

        return $entity->loadMissing($relations);
    }

    protected function extractChildItems(mixed $data, string $key): ?array
    {
        $value = match (true) {
            is_array($data) => $data[$key] ?? null,
            is_object($data) => $data->{$key} ?? null,
            default => null,
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

    private function childrenMeta(array $children): array
    {
        return $children === [] ? [] : ['children' => $children];
    }
}
