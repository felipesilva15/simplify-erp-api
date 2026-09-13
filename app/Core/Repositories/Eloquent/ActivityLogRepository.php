<?php

namespace App\Core\Repositories\Eloquent;

use App\Core\Helpers\ModelHelpers;
use App\Core\Models\ActivityLog;
use App\Core\Repositories\Interfaces\ActivityLogRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
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

    #[Override]
    public function listByModelAndId(string $model, mixed $id, array $params = []): LengthAwarePaginator {
        $originType = Relation::getMorphAlias($model) ?? $model;

        $query = $this->model::query()
            ->with('user')
            ->where('origin_type', $originType)
            ->where('origin_id', (string) $id);

        if (!empty($params['sorts'])) {
            $query = ModelHelpers::setSortsOnQuery($query, $params['sorts']);
        } else {
            $query->orderByDesc('created_at');
        }

        $perPage = isset($params['per_page']) ? (int) $params['per_page'] : 15;
        $page = isset($params['page']) ? (int) $params['page'] : 1;

        return $query->paginate(perPage: $perPage, page: $page)->withQueryString();
    }
}