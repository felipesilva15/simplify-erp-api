<?php

namespace App\Core\Services;

use App\Core\DTO\ServiceResult;
use App\Core\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseCrudService
{
    protected BaseRepositoryInterface $repository;

    public function store(mixed $data): ServiceResult {
        $entity = $this->repository->store($data);

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
        $entity = $this->repository->update($entity, $data);

        return new ServiceResult(
            data: $entity
        );
    }

    public function delete(Model $entity): ServiceResult {
        return new ServiceResult(
            data: null,
            meta: [
                'deleted' => $this->repository->delete($entity)
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

    public function lookup(array $params): ServiceResult {
        return new ServiceResult(
            data: $this->repository->lookup($params)
        );
    }
}