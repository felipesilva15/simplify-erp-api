<?php

namespace App\Modules\Catalog\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Core\Http\Requests\Core\LookupRequest;
use App\Modules\Catalog\Http\Resources\MeasurementUnit\MeasurementUnitLookupCollection;

use App\Core\Http\Controllers\Controller;
use App\Core\Http\Requests\Core\ListRequest;
use App\Modules\Catalog\Http\Requests\MeasurementUnit\StoreMeasurementUnitRequest;
use App\Modules\Catalog\Http\Requests\MeasurementUnit\UpdateMeasurementUnitRequest;
use App\Modules\Catalog\Http\Resources\MeasurementUnit\MeasurementUnitResource;
use App\Modules\Catalog\Http\Resources\MeasurementUnit\MeasurementUnitCollection;
use App\Modules\Catalog\DTO\MeasurementUnitDTO;
use App\Modules\Catalog\Models\MeasurementUnit;
use App\Modules\Catalog\Services\MeasurementUnitService;
use App\Core\Traits\HasActivityLogs;
use App\Core\Traits\HasExcelExport;
use App\Modules\Catalog\Exports\MeasurementUnitExport;
use InvalidArgumentException;

/**
 * @OA\PathItem(
 *     path="/api/catalog/measurement-units/{id}/activity-logs",
 *     @OA\Get(
 *         tags={"MeasurementUnit"},
 *         summary="List activity logs of a measurement unit",
 *         operationId="listMeasurementUnitActivityLogs",
 *         @OA\Parameter(
 *             name="id",
 *             in="path",
 *             required=true,
 *             description="MeasurementUnit ID",
 *             @OA\Schema(type="integer")
 *         ),
 *         @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Response(
 *             response="200",
 *             description="MeasurementUnit activity logs",
 *             @OA\JsonContent(
 *                 allOf={
 *                     @OA\Schema(ref="#/components/schemas/ApiResponse"),
 *                     @OA\Schema(ref="#/components/schemas/ActivityLogCollection")
 *                 }
 *             )
 *         ),
 *         @OA\Response(
 *             response="401",
 *             description="Unauthorized",
 *             @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
 *         ),
 *         @OA\Response(
 *             response="403",
 *             description="Forbidden",
 *             @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
 *         ),
 *         @OA\Response(
 *             response="404",
 *             description="Record not found",
 *             @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
 *         ),
 *         security={{"bearerAuth":{}}}
 *     )
 * )
 * @OA\PathItem(
 *     path="/api/catalog/measurement-units/export",
 *     @OA\Get(
 *         tags={"MeasurementUnit"},
 *         summary="Export all measurement units to Excel",
 *         operationId="exportMeasurementUnit",
 *         @OA\Parameter(ref="#/components/parameters/qParam"),
 *         @OA\Parameter(ref="#/components/parameters/sortsParam"),
 *         @OA\Parameter(ref="#/components/parameters/perPageParam"),
 *         @OA\Parameter(ref="#/components/parameters/pageParam"),
 *         @OA\Parameter(
 *             name="format",
 *             in="query",
 *             required=false,
 *             description="Export format (full, summarized, detailed)",
 *             @OA\Schema(type="string", enum={"full","summarized","detailed"})
 *         ),
 *         @OA\Parameter(
 *             name="extension",
 *             in="query",
 *             required=false,
 *             description="File extension (xlsx, xls, csv)",
 *             @OA\Schema(type="string", enum={"xlsx","xls","csv"})
 *         ),
 *     @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[code][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[code][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[code][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[symbol][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[symbol][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[symbol][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[category][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[category][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[category][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[base_unit_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[base_unit_id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[base_unit_id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[base_unit_id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[base_unit_id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[base_unit_id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[conversion_factor][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[conversion_factor][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[conversion_factor][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[conversion_factor][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[conversion_factor][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[conversion_factor][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[decimal_places][eq]", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="filters[decimal_places][ne]", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="filters[external_code][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[external_code][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[external_code][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[is_system][eq]", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="filters[is_system][ne]", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="filters[is_active][eq]", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="filters[is_active][ne]", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="filters[created_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][ne]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Response(
 *             response="200",
 *             description="Excel file with measurement units",
 *             @OA\MediaType(
 *                 mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
 *             )
 *         ),
 *         @OA\Response(
 *             response="401",
 *             description="Unauthorized",
 *             @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
 *         ),
 *         @OA\Response(
 *             response="403",
 *             description="Forbidden",
 *             @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
 *         ),
 *         security={{"bearerAuth":{}}}
 *     )
 * )
 */
class MeasurementUnitController extends Controller
{
    use HasActivityLogs;
    use HasExcelExport;

    protected MeasurementUnitService $service;

    public function __construct(MeasurementUnitService $service) {
        $this->service = $service;
        $this->authorizeResource(MeasurementUnit::class, 'measurement_unit');
    }

    protected function activityLogModelClass(): string
    {
        return MeasurementUnit::class;
    }

    /**
     * @OA\Get(
     *      path="/api/catalog/measurement-units",
     *      tags={"MeasurementUnit"},
     *      summary="List all measurement units",
     *     @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[code][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[code][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[code][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[symbol][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[symbol][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[symbol][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[category][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[category][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[category][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[base_unit_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[base_unit_id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[base_unit_id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[base_unit_id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[base_unit_id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[base_unit_id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[conversion_factor][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[conversion_factor][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[conversion_factor][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[conversion_factor][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[conversion_factor][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[conversion_factor][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[decimal_places][eq]", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="filters[decimal_places][ne]", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="filters[external_code][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[external_code][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[external_code][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[is_system][eq]", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="filters[is_system][ne]", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="filters[is_active][eq]", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="filters[is_active][ne]", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="filters[created_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(ref="#/components/parameters/qParam"),
     *     @OA\Parameter(ref="#/components/parameters/sortsParam"),
     *     @OA\Parameter(ref="#/components/parameters/perPageParam"),
     *     @OA\Parameter(ref="#/components/parameters/pageParam"),
     *      @OA\Response(
     *          response="200", 
     *          description="MeasurementUnit list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/MeasurementUnitCollection")
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="403", 
     *          description="Forbidden",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function index(ListRequest $request): JsonResponse {
        $serviceResult = $this->service->list($request->all());

        $paginated = new MeasurementUnitCollection($serviceResult->data);
        $paginated = $paginated->toArray($request);

        return $this->success(
            data: $paginated['data'],
            links: $paginated['links'],
            meta: $paginated['meta'],
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Get(
     *      path="/api/catalog/measurement-units/{measurement_unit}",
     *      tags={"MeasurementUnit"},
     *      summary="List a measurement unit by ID",
     *      @OA\Parameter(
     *         name="measurement_unit",
     *         in="path",
     *         required=true,
     *         description="MeasurementUnit ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="MeasurementUnit data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/MeasurementUnitResource"
     *                      )
     *                  )
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="403", 
     *          description="Forbidden",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function show(MeasurementUnit $measurement_unit): JsonResponse {
        $serviceResult = $this->service->show($measurement_unit);

        return $this->success(
            data: new MeasurementUnitResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *      path="/api/catalog/measurement-units",
     *      tags={"MeasurementUnit"},
     *      summary="Registers a measurement unit",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for creating a new measurement unit",
     *         @OA\JsonContent(ref="#/components/schemas/StoreMeasurementUnitRequest")
     *      ),
     *      @OA\Response(
     *          response="201", 
     *          description="Registered measurement unit data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/MeasurementUnitResource"
     *                      )
     *                  )
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="403", 
     *          description="Forbidden",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="422", 
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function store(StoreMeasurementUnitRequest $request): JsonResponse {
        $dto = MeasurementUnitDTO::fromArray($request->validated());
        $serviceResult = $this->service->store($dto);

        return $this->success(
            data: new MeasurementUnitResource($serviceResult->data),
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Get(
     *      path="/api/catalog/measurement-units/{measurement_unit}/edit",
     *      tags={"MeasurementUnit"},
     *      summary="Get data to edit a measurement unit",
     *      @OA\Parameter(
     *         name="measurement_unit",
     *         in="path",
     *         required=true,
     *         description="MeasurementUnit ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="MeasurementUnit data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/MeasurementUnitResource"
     *                      ),
     *                      @OA\Property(property="warnings", type="array", @OA\Items(type="string", example="Este recurso não pode ser editado."), nullable=true),
     *                      @OA\Property(
     *                          property="meta", 
     *                          type="object", 
     *                          @OA\Property(property="editable", type="boolean", example=true)
     *                      )
     *                  )
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="403", 
     *          description="Forbidden",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      )
     * )
     */
    public function edit(MeasurementUnit $measurement_unit): JsonResponse {
        $serviceResult = $this->service->edit($measurement_unit);

        return $this->success(
            data: new MeasurementUnitResource($serviceResult->data),
            warnings: $serviceResult->warnings,
            meta: $serviceResult->meta,
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Put(
     *      path="/api/catalog/measurement-units/{measurement_unit}",
     *      tags={"MeasurementUnit"},
     *      summary="Update a measurement unit",
     *      @OA\Parameter(
     *         name="measurement_unit",
     *         in="path",
     *         required=true,
     *         description="MeasurementUnit ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for update measurement unit",
     *         @OA\JsonContent(ref="#/components/schemas/UpdateMeasurementUnitRequest")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Updated measurement unit data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/MeasurementUnitResource"
     *                      )
     *                  )
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="403", 
     *          description="Forbidden",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="422", 
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function update(MeasurementUnit $measurement_unit, UpdateMeasurementUnitRequest $request): JsonResponse {
        $dto = MeasurementUnitDTO::fromArray($request->validated());
        $serviceResult = $this->service->update($measurement_unit, $dto);

        return $this->success(
            data: new MeasurementUnitResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/catalog/measurement-units/{measurement_unit}",
     *      tags={"MeasurementUnit"},
     *      summary="Delete a measurement unit",
     *      @OA\Parameter(
     *         name="measurement_unit",
     *         in="path",
     *         required=true,
     *         description="MeasurementUnit ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="204", 
     *          description="No content"
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="403", 
     *          description="Forbidden",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(MeasurementUnit $measurement_unit): Response {
        $this->service->delete($measurement_unit);
        return response()->noContent();
    }

    protected function exportModelClass(): string
    {
        return MeasurementUnit::class;
    }

    protected function exportClassForFormat(string $format): string
    {
        return match ($format) {
            'full' => MeasurementUnitExport::class,
            default => throw new InvalidArgumentException(
                "Formato de exportação [{$format}] não suportado."
            ),
        };
    }/**
     * @OA\Get(
     *      path="/api/catalog/measurement-units/lookup",
     *      tags={"MeasurementUnit"},
     *      summary="Lookup measurement units",
     *      @OA\Parameter(ref="#/components/parameters/qParam"),
     *      @OA\Parameter(name="keys[]", in="query", description="Keys to find", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(ref="#/components/parameters/sortsParam"),
     *      @OA\Parameter(ref="#/components/parameters/perPageParam"),
     *      @OA\Parameter(ref="#/components/parameters/pageParam"),
     *      @OA\Response(
     *          response="200", 
     *          description="measurement unit lookup list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/MeasurementUnitLookupCollection")
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function lookup(LookupRequest $request): JsonResponse {
        $serviceResult = $this->service->lookup($request->all());

        $paginated = new MeasurementUnitLookupCollection($serviceResult->data);
        $paginated = $paginated->toArray($request);

        return $this->success(
            data: $paginated['data'],
            links: $paginated['links'],
            meta: $paginated['meta'],
            httpStatus: Response::HTTP_OK
        );
    }
}