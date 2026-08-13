<?php

namespace App\Core\Repositories\Eloquent;

use App\Core\Models\ActivityLog;
use App\Core\Repositories\Interfaces\ActivityLogRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Override;

class ActivityLogRepository extends BaseRepository implements ActivityLogRepositoryInterface
{
    #[Override]
    protected function getModelClass(): string
    {
        return ActivityLog::class;
    }

    #[Override]
    public function update(Model $entity, mixed $data): ?Model
    {
        throw new \BadFunctionCallException('Logs de atividade não podem ser atualizados.');
    }

    #[Override]
    public function delete(Model $entity): bool {
        throw new \BadFunctionCallException('Logs de atividade não podem ser excluídos.');
    }

    #[Override]
    public function lookup(array $params = []): LengthAwarePaginator {
        throw new \BadFunctionCallException('Logs de atividade não possuem lookup.');
    }
}