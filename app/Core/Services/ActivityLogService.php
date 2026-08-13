<?php

namespace App\Core\Services;

use App\Core\DTO\ActivityLogDTO;
use App\Core\DTO\ServiceResult;
use App\Core\Enums\ActivityActionEnum;
use App\Core\Repositories\Interfaces\ActivityLogRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Override;

class ActivityLogService
{
    protected ActivityLogRepositoryInterface $repository;

    public function __construct(ActivityLogRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function log(Model $model, ActivityActionEnum $action, ?string $description = null): void {
        $this->repository->store(ActivityLogDTO::fromArray([
            'origin_type'   => $model::class,
            'origin_id'     => (string) $model->getKey(),
            'action'        => $action,
            'user_id'       => auth()?->id(),
            'description'   => $description ?? $this->defaultActivityDescription($action),
            'route_name'    => request()?->route()?->getName(),
            'route_path'    => request()?->path(),
            'ip_address'    => request()?->ip(),
            'user_agent'    => request()?->userAgent(),
        ]));
    }

    public function logById(string $modelClass, int|string $id, ActivityActionEnum $action, ?string $description = null): void
    {
        $this->repository->store(ActivityLogDTO::fromArray([
            'origin_type'   => $modelClass,
            'origin_id'     => (string) $id,
            'action'        => $action,
            'user_id'       => auth()?->id(),
            'description'   => $description ?? $this->defaultActivityDescription($action),
            'route_name'    => request()?->route()?->getName(),
            'route_path'    => request()?->path(),
            'ip_address'    => request()?->ip(),
            'user_agent'    => request()?->userAgent(),
        ]));
    }

    protected function defaultActivityDescription(ActivityActionEnum $action): string
    {
        return match ($action) {
            ActivityActionEnum::Created => 'Registro criado',
            ActivityActionEnum::Updated => 'Registro alterado',
            ActivityActionEnum::Deleted => 'Registro excluído',
            ActivityActionEnum::Approved => 'Registro aprovado',
            default => 'Realizou uma ação',
        };
    }

    public function list(array $filters = []): ServiceResult {
        return new ServiceResult(
            data: $this->repository->list($filters)
        );
    }
}