<?php

namespace App\Modules\Catalog\Http\Controllers;

use App\Core\Enums\ProductTypeEnum;
use App\Core\Exceptions\NotFoundHttpException;
use App\Core\Http\Controllers\Controller;
use App\Core\Http\Requests\Core\ListRequest;
use App\Core\Http\Requests\Core\LookupRequest;
use App\Core\Traits\HasActivityLogs;
use App\Core\Traits\HasExcelExport;
use App\Modules\Catalog\DTO\ProductDTO;
use App\Modules\Catalog\Exports\ServiceExport;
use App\Modules\Catalog\Http\Requests\Service\StoreServiceRequest;
use App\Modules\Catalog\Http\Requests\Service\UpdateServiceRequest;
use App\Modules\Catalog\Http\Resources\Service\ServiceCollection;
use App\Modules\Catalog\Http\Resources\Service\ServiceLookupCollection;
use App\Modules\Catalog\Http\Resources\Service\ServiceResource;
use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Services\ProductService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use InvalidArgumentException;

/**
 * @OA\PathItem(
 *     path="/api/catalog/services/{id}/activity-logs",
 *
 *     @OA\Get(
 *         tags={"Service"},
 *         summary="List activity logs of a service",
 *         operationId="listServiceActivityLogs",
 *
 *         @OA\Parameter(
 *             name="id",
 *             in="path",
 *             required=true,
 *             description="Service ID",
 *
 *             @OA\Schema(type="integer")
 *         ),
 *
 *         @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
 *
 *         @OA\Response(
 *             response="200",
 *             description="Service activity logs",
 *
 *             @OA\JsonContent(
 *                 allOf={
 *
 *                     @OA\Schema(ref="#/components/schemas/ApiResponse"),
 *                     @OA\Schema(ref="#/components/schemas/ActivityLogCollection")
 *                 }
 *             )
 *         ),
 *
 *         @OA\Response(response="401", description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
 *         @OA\Response(response="403", description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
 *         @OA\Response(response="404", description="Record not found", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
 *         security={{"bearerAuth":{}}}
 *     )
 * )
 *
 * @OA\PathItem(
 *     path="/api/catalog/services/export",
 *
 *     @OA\Get(
 *         tags={"Service"},
 *         summary="Export all services to Excel",
 *         operationId="exportService",
 *
 *         @OA\Parameter(ref="#/components/parameters/qParam"),
 *         @OA\Parameter(ref="#/components/parameters/sortsParam"),
 *         @OA\Parameter(ref="#/components/parameters/perPageParam"),
 *         @OA\Parameter(ref="#/components/parameters/pageParam"),
 *         @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="filters[item_type_code][eq]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[sku][eq]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[name][eq]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="format", in="query", required=false, @OA\Schema(type="string", enum={"full"})),
 *         @OA\Parameter(name="extension", in="query", required=false, @OA\Schema(type="string", enum={"xlsx","xls","csv"})),
 *
 *         @OA\Response(
 *             response="200",
 *             description="Excel file with services",
 *
 *             @OA\MediaType(mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
 *         ),
 *
 *         @OA\Response(response="401", description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
 *         @OA\Response(response="403", description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
 *         security={{"bearerAuth":{}}}
 *     )
 * )
 */
class ServiceController extends Controller
{
    use HasActivityLogs;
    use HasExcelExport;

    protected ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
        $this->authorizeResource(Product::class, 'service');
    }

    protected function resourceAbilityMap(): array
    {
        return [
            'index' => 'viewAnyService',
            'show' => 'viewService',
            'create' => 'createService',
            'store' => 'createService',
            'edit' => 'updateService',
            'update' => 'updateService',
            'destroy' => 'deleteService',
        ];
    }

    protected function activityLogModelClass(): string
    {
        return Product::class;
    }

    protected function validateActivityLogModel(Model $model): void
    {
        if ((string) $model->type !== ProductTypeEnum::Service->value) {
            throw new NotFoundHttpException;
        }
    }

    protected function activityLogAbility(): string
    {
        return 'viewService';
    }

    protected function exportAbility(): string
    {
        return 'exportService';
    }

    protected function productType(): ProductTypeEnum
    {
        return ProductTypeEnum::Service;
    }

    /**
     * @OA\Get(
     *      path="/api/catalog/services",
     *      tags={"Service"},
     *      summary="List all services",
     *      operationId="listServices",
     *
     *      @OA\Parameter(ref="#/components/parameters/qParam"),
     *      @OA\Parameter(ref="#/components/parameters/sortsParam"),
     *      @OA\Parameter(ref="#/components/parameters/perPageParam"),
     *      @OA\Parameter(ref="#/components/parameters/pageParam"),
     *      @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[sku][eq]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[name][eq]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[is_active][eq]", in="query", required=false, @OA\Schema(type="boolean")),
     *
     *      @OA\Response(
     *          response="200",
     *          description="Services list",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ServiceCollection")
     *      ),
     *
     *      @OA\Response(response="401", description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      @OA\Response(response="403", description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function index(ListRequest $request): JsonResponse
    {
        $serviceResult = $this->service->listFor($this->productType(), $request->all());

        $paginated = (new ServiceCollection($serviceResult->data))->toArray($request);

        return $this->success(
            data: $paginated['data'],
            links: $paginated['links'],
            meta: $paginated['meta'],
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *      path="/api/catalog/services/{service}",
     *      tags={"Service"},
     *      summary="Get a service by ID",
     *      operationId="getService",
     *
     *      @OA\Parameter(name="service", in="path", required=true, description="Service ID", @OA\Schema(type="integer")),
     *
     *      @OA\Response(
     *          response="200",
     *          description="Service data",
     *
     *          @OA\JsonContent(
     *              allOf={
     *
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(@OA\Property(property="data", ref="#/components/schemas/ServiceResource"))
     *              }
     *          )
     *      ),
     *
     *      @OA\Response(response="401", description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      @OA\Response(response="403", description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      @OA\Response(response="404", description="Record not found", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function show(Product $service): JsonResponse
    {
        $serviceResult = $this->service->showFor($this->productType(), $service);

        return $this->success(
            data: new ServiceResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *      path="/api/catalog/services",
     *      tags={"Service"},
     *      summary="Registers a service",
     *      operationId="storeService",
     *
     *      @OA\RequestBody(required=true, description="Data for creating a new service", @OA\JsonContent(ref="#/components/schemas/StoreServiceRequest")),
     *
     *      @OA\Response(
     *          response="201",
     *          description="Registered service data",
     *
     *          @OA\JsonContent(
     *              allOf={
     *
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(@OA\Property(property="data", ref="#/components/schemas/ServiceResource"))
     *              }
     *          )
     *      ),
     *
     *      @OA\Response(response="401", description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      @OA\Response(response="403", description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      @OA\Response(response="422", description="Unprocessable Entity", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        $dto = ProductDTO::fromArray($request->validated());
        $dto->fieldsToUse = array_keys($request->validated());
        $serviceResult = $this->service->storeFor($this->productType(), $dto);

        return $this->success(
            data: new ServiceResource($serviceResult->data),
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Get(
     *      path="/api/catalog/services/{service}/edit",
     *      tags={"Service"},
     *      summary="Get data to edit a service",
     *      operationId="editService",
     *
     *      @OA\Parameter(name="service", in="path", required=true, description="Service ID", @OA\Schema(type="integer")),
     *
     *      @OA\Response(
     *          response="200",
     *          description="Service data",
     *
     *          @OA\JsonContent(
     *              allOf={
     *
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(@OA\Property(property="data", ref="#/components/schemas/ServiceResource"))
     *              }
     *          )
     *      ),
     *
     *      @OA\Response(response="401", description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      @OA\Response(response="403", description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      @OA\Response(response="404", description="Record not found", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function edit(Product $service): JsonResponse
    {
        $serviceResult = $this->service->editFor($this->productType(), $service);

        return $this->success(
            data: new ServiceResource($serviceResult->data),
            warnings: $serviceResult->warnings,
            meta: $serviceResult->meta,
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Put(
     *      path="/api/catalog/services/{service}",
     *      tags={"Service"},
     *      summary="Update a service",
     *      operationId="updateService",
     *
     *      @OA\Parameter(name="service", in="path", required=true, description="Service ID", @OA\Schema(type="integer")),
     *
     *      @OA\RequestBody(required=true, description="Data for update service", @OA\JsonContent(ref="#/components/schemas/UpdateServiceRequest")),
     *
     *      @OA\Response(
     *          response="200",
     *          description="Updated service data",
     *
     *          @OA\JsonContent(
     *              allOf={
     *
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(@OA\Property(property="data", ref="#/components/schemas/ServiceResource"))
     *              }
     *          )
     *      ),
     *
     *      @OA\Response(response="401", description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      @OA\Response(response="403", description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      @OA\Response(response="404", description="Record not found", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      @OA\Response(response="422", description="Unprocessable Entity", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function update(Product $service, UpdateServiceRequest $request): JsonResponse
    {
        $dto = ProductDTO::fromArray($request->validated());
        $dto->fieldsToUse = array_keys($request->validated());
        $serviceResult = $this->service->updateFor($this->productType(), $service, $dto);

        return $this->success(
            data: new ServiceResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/catalog/services/{service}",
     *      tags={"Service"},
     *      summary="Delete a service",
     *      operationId="deleteService",
     *
     *      @OA\Parameter(name="service", in="path", required=true, description="Service ID", @OA\Schema(type="integer")),
     *
     *      @OA\Response(response="204", description="No content"),
     *      @OA\Response(response="401", description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      @OA\Response(response="403", description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      @OA\Response(response="404", description="Record not found", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(Product $service): Response
    {
        $this->service->deleteFor($this->productType(), $service);

        return response()->noContent();
    }

    /**
     * @OA\Get(
     *      path="/api/catalog/services/lookup",
     *      tags={"Service"},
     *      summary="Lookup services",
     *      operationId="lookupServices",
     *
     *      @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[created_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[created_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[created_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[created_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[created_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[created_at][ne]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[updated_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[updated_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[updated_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[updated_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[updated_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[updated_at][ne]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[deleted_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[deleted_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[deleted_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[deleted_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[deleted_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[deleted_at][ne]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[name][eq]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[name][like]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[name][ne]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[sku][eq]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[sku][like]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[sku][ne]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[item_type_code][eq]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[item_type_code][like]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[item_type_code][ne]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[is_active][eq]", in="query", required=false, @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="filters[is_active][ne]", in="query", required=false, @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="filters[product_category_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[product_category_id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[product_category_id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[product_category_id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[product_category_id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[product_category_id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[product_brand_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[product_brand_id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[product_brand_id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[product_brand_id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[product_brand_id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[product_brand_id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[measurement_unit_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[measurement_unit_id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[measurement_unit_id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[measurement_unit_id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[measurement_unit_id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[measurement_unit_id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(ref="#/components/parameters/qParam"),
     *      @OA\Parameter(name="keys[]", in="query", description="Keys to find", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(ref="#/components/parameters/sortsParam"),
     *      @OA\Parameter(ref="#/components/parameters/perPageParam"),
     *      @OA\Parameter(ref="#/components/parameters/pageParam"),
     *
     *      @OA\Response(
     *          response="200",
     *          description="Service lookup list",
     *
     *          @OA\JsonContent(
     *              allOf={
     *
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/ServiceLookupCollection")
     *              }
     *          )
     *      ),
     *
     *      @OA\Response(response="401", description="Unauthorized", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      @OA\Response(response="403", description="Forbidden", @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function lookup(LookupRequest $request): JsonResponse
    {
        $this->authorize('viewAnyService', Product::class);

        $serviceResult = $this->service->lookupFor($this->productType(), $request->all());

        $paginated = (new ServiceLookupCollection($serviceResult->data))->toArray($request);

        return $this->success(
            data: $paginated['data'],
            links: $paginated['links'],
            meta: $paginated['meta'],
            httpStatus: Response::HTTP_OK
        );
    }

    protected function exportModelClass(): string
    {
        return Product::class;
    }

    protected function exportQuery(array $params): Builder
    {
        return $this->service->exportQueryFor($this->productType(), $params);
    }

    protected function exportFileName(string $format, string $extension): string
    {
        $baseName = $format === $this->defaultExportFormat()
            ? 'service'
            : "service-{$format}";

        return "{$baseName}.{$extension}";
    }

    protected function exportClassForFormat(string $format): string
    {
        return match ($format) {
            'full' => ServiceExport::class,
            default => throw new InvalidArgumentException(
                "Formato de exportação [{$format}] não suportado."
            ),
        };
    }
}
