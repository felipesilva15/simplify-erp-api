<?php

namespace App\Modules\Integration\Services;

use App\Core\DTO\ServiceResult;
use App\Core\Services\BaseCrudService;
use App\Modules\Integration\Models\IntegrationConnection;
use Illuminate\Database\Eloquent\Model;

class IntegrationConnectionService extends BaseCrudService
{
    public function __construct(IntegrationConnectionRepository $repository)
    {
        $this->repository = $repository;
        $this->modelClass = IntegrationConnection::class;
        $this->dtoClass = null;
    }

    public function list(array $filters = []): ServiceResult
    {
        $result = $this->repository->list($filters);
        return new ServiceResult(data: $result->data, meta: $result->meta ?? null);
    }

    public function show(Model|int $modelOrId): ServiceResult
    {
        $model = $modelOrId instanceof Model ? $modelOrId : $this->repository->find($modelOrId);
        return new ServiceResult(data: $model);
    }

    public function store(array|object $data): ServiceResult
    {
        $dataArr = is_object($data) ? (array)$data : $data;
        // credentials write-only; keep as array for encrypted cast
        $model = IntegrationConnection::create($dataArr);
        app(\App\Core\Services\ActivityLogService::class)->log($model, \App\Core\Enums\ActivityActionEnum::Created);
        return new ServiceResult(data: $model);
    }

    public function update(Model|int $modelOrId, array|object $data): ServiceResult
    {
        $model = $modelOrId instanceof Model ? $modelOrId : $this->repository->find($modelOrId);
        $dataArr = is_object($data) ? (array)$data : $data;
        // If credentials not provided, don't overwrite
        if (array_key_exists('credentials', $dataArr) && empty($dataArr['credentials'])) {
            unset($dataArr['credentials']);
        }
        $model->fill($dataArr)->save();
        app(\App\Core\Services\ActivityLogService::class)->log($model, \App\Core\Enums\ActivityActionEnum::Updated);
        return new ServiceResult(data: $model);
    }

    public function delete(Model|int $modelOrId): ServiceResult
    {
        $model = $modelOrId instanceof Model ? $modelOrId : $this->repository->find($modelOrId);
        $model->delete();
        app(\App\Core\Services\ActivityLogService::class)->log($model, \App\Core\Enums\ActivityActionEnum::Deleted);
        return new ServiceResult(data: true);
    }

    public function edit(Model|int $modelOrId): ServiceResult
    {
        $model = $modelOrId instanceof Model ? $modelOrId : $this->repository->find($modelOrId);
        return new ServiceResult(data: $model, warnings: null, meta: ['editable' => true]);
    }
}
