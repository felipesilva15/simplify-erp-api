<?php

namespace App\Core\Traits;

use App\Core\Http\Requests\Core\ListRequest;
use App\Core\Http\Resources\ActivityLog\ActivityLogCollection;
use App\Core\Services\ActivityLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait HasActivityLogs
{
    public function activityLogs(mixed $id, ListRequest $request): JsonResponse
    {
        $modelClass = $this->activityLogModelClass();
        $model = $modelClass::findOrFail($id);

        $this->validateActivityLogModel($model);

        $this->authorize($this->activityLogAbility(), $model);

        $serviceResult = app(ActivityLogService::class)->listByModelAndId(
            $modelClass,
            $model->getKey(),
            $request->all()
        );

        $paginated = (new ActivityLogCollection($serviceResult->data))->toArray($request);

        return $this->success(
            data: $paginated['data'],
            links: $paginated['links'],
            meta: $paginated['meta'],
            httpStatus: Response::HTTP_OK
        );
    }

    abstract protected function activityLogModelClass(): string;

    /**
     * Ability verificada antes de consultar o histórico. Fachadas sobre a mesma
     * entidade podem sobrescrever este método para usar uma permissão própria.
     */
    protected function activityLogAbility(): string
    {
        return 'view';
    }

    /**
     * Validação adicional da entidade antes de expor seu histórico.
     */
    protected function validateActivityLogModel(Model $model): void
    {
        //
    }
}
