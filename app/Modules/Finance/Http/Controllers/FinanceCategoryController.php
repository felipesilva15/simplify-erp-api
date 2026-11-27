<?php

namespace App\Modules\Finance\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Core\Http\Requests\Core\LookupRequest;
use App\Modules\Finance\Http\Resources\FinanceCategory\FinanceCategoryLookupCollection;

use App\Core\Http\Controllers\Controller;
use App\Core\Http\Requests\Core\ListRequest;
use App\Modules\Finance\Http\Requests\FinanceCategory\StoreFinanceCategoryRequest;
use App\Modules\Finance\Http\Requests\FinanceCategory\UpdateFinanceCategoryRequest;
use App\Modules\Finance\Http\Resources\FinanceCategory\FinanceCategoryResource;
use App\Modules\Finance\Http\Resources\FinanceCategory\FinanceCategoryCollection;
use App\Modules\Finance\DTO\FinanceCategoryDTO;
use App\Modules\Finance\Models\FinanceCategory;
use App\Modules\Finance\Services\FinanceCategoryService;
use App\Core\Traits\HasActivityLogs;
use App\Core\Traits\HasExcelExport;
use App\Modules\Finance\Exports\FinanceCategoryExport;
use InvalidArgumentException;

/**
 * @OA\PathItem(
 *     path="/api/finance/finance-categories/{id}/activity-logs",
 *     @OA\Get(
 *         tags={"FinanceCategory"},
 *         summary="List activity logs of a finance category",
 *         operationId="listFinanceCategoryActivityLogs",
 *         @OA\Parameter(
 *             name="id",
 *             in="path",
 *             required=true,
 *             description="FinanceCategory ID",
 *             @OA\Schema(type="integer")
 *         ),
 *         @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Response(
 *             response="200",
 *             description="FinanceCategory activity logs",
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
 *     path="/api/finance/finance-categories/export",
 *     @OA\Get(
 *         tags={"FinanceCategory"},
 *         summary="Export all finance categories to Excel",
 *         operationId="exportFinanceCategory",
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
 *         @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="filters[id][lt]", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="filters[id][lte]", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="filters[id][gt]", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="filters[id][gte]", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="filters[id][ne]", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="filters[parent_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="filters[parent_id][lt]", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="filters[parent_id][lte]", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="filters[parent_id][gt]", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="filters[parent_id][gte]", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="filters[parent_id][ne]", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="filters[code][eq]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[code][like]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[code][ne]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[name][eq]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[name][like]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[name][ne]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[applicable_type][eq]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[applicable_type][like]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[applicable_type][ne]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[created_at][eq]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[created_at][lt]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[created_at][lte]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[created_at][gt]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[created_at][gte]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[created_at][ne]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[updated_at][eq]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[updated_at][lt]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[updated_at][lte]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[updated_at][gt]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[updated_at][gte]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[updated_at][ne]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[deleted_at][eq]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[deleted_at][lt]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[deleted_at][lte]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[deleted_at][gt]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[deleted_at][gte]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Parameter(name="filters[deleted_at][ne]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Response(
 *             response="200",
 *             description="Excel file with finance categories",
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
class FinanceCategoryController extends Controller
{
    use HasActivityLogs;
    use HasExcelExport;

    protected FinanceCategoryService $service;

    public function __construct(FinanceCategoryService $service) {
        $this->service = $service;
        $this->authorizeResource(FinanceCategory::class, 'finance_category');
    }

    protected function activityLogModelClass(): string
    {
        return FinanceCategory::class;
    }

    /**
     * @OA\Get(
     *      path="/api/finance/finance-categories",
     *      tags={"FinanceCategory"},
     *      summary="List all finance categories",
     *      @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[parent_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[parent_id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[parent_id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[parent_id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[parent_id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[parent_id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[code][eq]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[code][like]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[code][ne]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[name][eq]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[name][like]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[name][ne]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[applicable_type][eq]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[applicable_type][like]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[applicable_type][ne]", in="query", required=false, @OA\Schema(type="string")),
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
     *      @OA\Parameter(ref="#/components/parameters/qParam"),
     *      @OA\Parameter(ref="#/components/parameters/sortsParam"),
     *      @OA\Parameter(ref="#/components/parameters/perPageParam"),
     *      @OA\Parameter(ref="#/components/parameters/pageParam"),
     *      @OA\Response(
     *          response="200", 
     *          description="FinanceCategory list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/FinanceCategoryCollection")
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

        $paginated = new FinanceCategoryCollection($serviceResult->data);
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
     *      path="/api/finance/finance-categories/{finance_category}",
     *      tags={"FinanceCategory"},
     *      summary="List a finance category by ID",
     *      @OA\Parameter(
     *         name="finance_category",
     *         in="path",
     *         required=true,
     *         description="FinanceCategory ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="FinanceCategory data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/FinanceCategoryResource"
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
    public function show(FinanceCategory $finance_category): JsonResponse {
        $serviceResult = $this->service->show($finance_category);

        return $this->success(
            data: new FinanceCategoryResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *      path="/api/finance/finance-categories",
     *      tags={"FinanceCategory"},
     *      summary="Registers a finance category",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for creating a new finance category",
     *         @OA\JsonContent(ref="#/components/schemas/StoreFinanceCategoryRequest")
     *      ),
     *      @OA\Response(
     *          response="201", 
     *          description="Registered finance category data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/FinanceCategoryResource"
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
    public function store(StoreFinanceCategoryRequest $request): JsonResponse {
        $dto = FinanceCategoryDTO::fromArray($request->validated());
        $serviceResult = $this->service->store($dto);

        return $this->success(
            data: new FinanceCategoryResource($serviceResult->data),
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Get(
     *      path="/api/finance/finance-categories/{finance_category}/edit",
     *      tags={"FinanceCategory"},
     *      summary="Get data to edit a finance category",
     *      @OA\Parameter(
     *         name="finance_category",
     *         in="path",
     *         required=true,
     *         description="FinanceCategory ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="FinanceCategory data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/FinanceCategoryResource"
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
    public function edit(FinanceCategory $finance_category): JsonResponse {
        $serviceResult = $this->service->edit($finance_category);

        return $this->success(
            data: new FinanceCategoryResource($serviceResult->data),
            warnings: $serviceResult->warnings,
            meta: $serviceResult->meta,
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Put(
     *      path="/api/finance/finance-categories/{finance_category}",
     *      tags={"FinanceCategory"},
     *      summary="Update a finance category",
     *      @OA\Parameter(
     *         name="finance_category",
     *         in="path",
     *         required=true,
     *         description="FinanceCategory ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for update finance category",
     *         @OA\JsonContent(ref="#/components/schemas/UpdateFinanceCategoryRequest")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Updated finance category data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/FinanceCategoryResource"
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
    public function update(FinanceCategory $finance_category, UpdateFinanceCategoryRequest $request): JsonResponse {
        $dto = FinanceCategoryDTO::fromArray($request->validated());
        $serviceResult = $this->service->update($finance_category, $dto);

        return $this->success(
            data: new FinanceCategoryResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/finance/finance-categories/{finance_category}",
     *      tags={"FinanceCategory"},
     *      summary="Delete a finance category",
     *      @OA\Parameter(
     *         name="finance_category",
     *         in="path",
     *         required=true,
     *         description="FinanceCategory ID",
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
    public function destroy(FinanceCategory $finance_category): Response {
        $this->service->delete($finance_category);
        return response()->noContent();
    }

    protected function exportModelClass(): string
    {
        return FinanceCategory::class;
    }

    protected function exportClassForFormat(string $format): string
    {
        return match ($format) {
            'full' => FinanceCategoryExport::class,
            default => throw new InvalidArgumentException(
                "Formato de exportação [{$format}] não suportado."
            ),
        };
    }/**
     * @OA\Get(
     *      path="/api/finance/finance-categories/lookup",
     *      tags={"FinanceCategory"},
     *      summary="Lookup finance categories",
     *      @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[parent_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[parent_id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[parent_id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[parent_id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[parent_id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[parent_id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[code][eq]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[code][like]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[code][ne]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[name][eq]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[name][like]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[name][ne]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[applicable_type][eq]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[applicable_type][like]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[applicable_type][ne]", in="query", required=false, @OA\Schema(type="string")),
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
     *      @OA\Parameter(ref="#/components/parameters/qParam"),
     *      @OA\Parameter(name="keys[]", in="query", description="Keys to find", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(ref="#/components/parameters/sortsParam"),
     *      @OA\Parameter(ref="#/components/parameters/perPageParam"),
     *      @OA\Parameter(ref="#/components/parameters/pageParam"),
     *      @OA\Response(
     *          response="200", 
     *          description="finance category lookup list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/FinanceCategoryLookupCollection")
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

        $paginated = new FinanceCategoryLookupCollection($serviceResult->data);
        $paginated = $paginated->toArray($request);

        return $this->success(
            data: $paginated['data'],
            links: $paginated['links'],
            meta: $paginated['meta'],
            httpStatus: Response::HTTP_OK
        );
    }
}