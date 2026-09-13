<?php

namespace App\Core\Traits;

use App\Core\Http\Requests\Core\ListRequest;
use App\Core\Http\Resources\ActivityLog\ActivityLogCollection;
use App\Core\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait HasActivityLogs
{
    public function activityLogs(mixed $id, ListRequest $request): JsonResponse
    {
        $modelClass = $this->activityLogModelClass();
        $model = $modelClass::findOrFail($id);

        $this->authorize('view', $model);

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
}