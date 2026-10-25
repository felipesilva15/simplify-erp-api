<?php

namespace App\Modules\Integration\Http\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Core\Http\Requests\Core\ListRequest;
use App\Core\Traits\HasActivityLogs;
use App\Modules\Integration\Http\Requests\IntegrationConnection\StoreIntegrationConnectionRequest;
use App\Modules\Integration\Http\Requests\IntegrationConnection\UpdateIntegrationConnectionRequest;
use App\Modules\Integration\Http\Resources\IntegrationConnection\IntegrationConnectionCollection;
use App\Modules\Integration\Http\Resources\IntegrationConnection\IntegrationConnectionResource;
use App\Modules\Integration\Models\IntegrationConnection;
use App\Modules\Integration\Services\IntegrationConnectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

class IntegrationConnectionController extends Controller
{
    use HasActivityLogs;

    protected IntegrationConnectionService $service;

    public function __construct(IntegrationConnectionService $service)
    {
        $this->service = $service;
        $this->authorizeResource(IntegrationConnection::class, 'integration_connection');
    }

    protected function activityLogModelClass(): string
    {
        return IntegrationConnection::class;
    }

    /**
     * @OA\Get(
     *     path="/api/integration-connections",
     *     tags={"IntegrationConnection"},
     *     summary="List integration connections",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List", @OA\JsonContent(ref="#/components/schemas/ApiResponse"))
     * )
     */
    public function index(ListRequest $request): JsonResponse
    {
        $serviceResult = $this->service->list($request->all());
        $paginated = (new IntegrationConnectionCollection($serviceResult->data))->toArray($request);

        return $this->success(
            data: $paginated['data'],
            links: $paginated['links'],
            meta: $paginated['meta'],
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *     path="/api/integration-connections",
     *     tags={"IntegrationConnection"},
     *     summary="Create integration connection",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreIntegrationConnectionRequest")),
     *     @OA\Response(response="201", description="Created", @OA\JsonContent(ref="#/components/schemas/ApiResponse"))
     * )
     */
    public function store(StoreIntegrationConnectionRequest $request): JsonResponse
    {
        $serviceResult = $this->service->store($request->validated());
        return $this->success(
            data: new IntegrationConnectionResource($serviceResult->data),
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Get(
     *     path="/api/integration-connections/{integration_connection}",
     *     tags={"IntegrationConnection"},
     *     summary="Show integration connection",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="Show", @OA\JsonContent(ref="#/components/schemas/ApiResponse"))
     * )
     */
    public function show(IntegrationConnection $integration_connection): JsonResponse
    {
        $serviceResult = $this->service->show($integration_connection);
        return $this->success(
            data: new IntegrationConnectionResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    public function edit(IntegrationConnection $integration_connection): JsonResponse
    {
        $serviceResult = $this->service->edit($integration_connection);
        return $this->success(
            data: new IntegrationConnectionResource($serviceResult->data),
            warnings: $serviceResult->warnings ?? null,
            meta: $serviceResult->meta ?? null,
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Put(
     *     path="/api/integration-connections/{integration_connection}",
     *     tags={"IntegrationConnection"},
     *     summary="Update integration connection",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateIntegrationConnectionRequest")),
     *     @OA\Response(response="200", description="Updated", @OA\JsonContent(ref="#/components/schemas/ApiResponse"))
     * )
     */
    public function update(UpdateIntegrationConnectionRequest $request, IntegrationConnection $integration_connection): JsonResponse
    {
        $serviceResult = $this->service->update($integration_connection, $request->validated());
        return $this->success(
            data: new IntegrationConnectionResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    public function destroy(IntegrationConnection $integration_connection): Response
    {
        $this->service->delete($integration_connection);
        return response()->noContent();
    }
}
