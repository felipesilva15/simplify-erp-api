<?php

namespace App\Core\Services;

use App\Core\DTO\ServiceResult;
use App\Core\Enums\ActivityActionEnum;
use App\Core\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseCrudService
{
    protected BaseRepositoryInterface $repository;
    protected ActivityLogService $activity;

    public function store(mixed $data): ServiceResult {
        $data = $this->prepareData($data);
        $entity = $this->repository->store($data);
        $this->activity->log($entity, ActivityActionEnum::Created);

        return new ServiceResult(
            data: $entity
        );
    }

    public function edit(Model $entity): ServiceResult {
        return new ServiceResult(
            data: $entity,
            meta: [
                'editable' => true,
                'warnings' => []
            ]
        );
    }

    public function update(Model $entity, mixed $data): ServiceResult {
        $data = $this->prepareData($data);
        $entity = $this->repository->update($entity, $data);
        $this->activity->log($entity, ActivityActionEnum::Updated);

        return new ServiceResult(
            data: $entity
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

    public function show(Model $entity): ServiceResult {
        return new ServiceResult(
            data: $entity
        );
    }

    public function find(mixed $id): ServiceResult {
        return new ServiceResult(
            data: $this->repository->getById($id)
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
}