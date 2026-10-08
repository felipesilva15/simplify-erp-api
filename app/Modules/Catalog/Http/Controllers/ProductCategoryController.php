<?php

namespace App\Modules\Catalog\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Core\Http\Requests\Core\LookupRequest;
use App\Modules\Catalog\Http\Resources\ProductCategory\ProductCategoryLookupCollection;

use App\Core\Http\Controllers\Controller;
use App\Core\Http\Requests\Core\ListRequest;
use App\Modules\Catalog\Http\Requests\ProductCategory\StoreProductCategoryRequest;
use App\Modules\Catalog\Http\Requests\ProductCategory\UpdateProductCategoryRequest;
use App\Modules\Catalog\Http\Resources\ProductCategory\ProductCategoryResource;
use App\Modules\Catalog\Http\Resources\ProductCategory\ProductCategoryCollection;
use App\Modules\Catalog\DTO\ProductCategoryDTO;
use App\Modules\Catalog\Models\ProductCategory;
use App\Modules\Catalog\Services\ProductCategoryService;
use App\Core\Traits\HasActivityLogs;
use App\Core\Traits\HasExcelExport;
use App\Modules\Catalog\Exports\ProductCategoryExport;
use InvalidArgumentException;

/**
 * @OA\PathItem(
 *     path="/api/catalog/product-categories/{id}/activity-logs",
 *     @OA\Get(
 *         tags={"ProductCategory"},
 *         summary="List activity logs of a product category",
 *         operationId="listProductCategoryActivityLogs",
 *         @OA\Parameter(
 *             name="id",
 *             in="path",
 *             required=true,
 *             description="ProductCategory ID",
 *             @OA\Schema(type="integer")
 *         ),
 *         @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Response(
 *             response="200",
 *             description="ProductCategory activity logs",
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
 *     path="/api/catalog/product-categories/export",
 *     @OA\Get(
 *         tags={"ProductCategory"},
 *         summary="Export all product categories to Excel",
 *         operationId="exportProductCategory",
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
     *     @OA\Parameter(name="filters[name][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[parent_category_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[parent_category_id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[parent_category_id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[parent_category_id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[parent_category_id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[parent_category_id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[applicability][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[applicability][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[applicability][ne]", in="query", required=false, @OA\Schema(type="string")),
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
 *             description="Excel file with product categories",
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
class ProductCategoryController extends Controller
{
    use HasActivityLogs;
    use HasExcelExport;

    protected ProductCategoryService $service;

    public function __construct(ProductCategoryService $service) {
        $this->service = $service;
        $this->authorizeResource(ProductCategory::class, 'product_category');
    }

    protected function activityLogModelClass(): string
    {
        return ProductCategory::class;
    }

    /**
     * @OA\Get(
     *      path="/api/catalog/product-categories",
     *      tags={"ProductCategory"},
     *      summary="List all product categories",
     *     @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[name][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[parent_category_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[parent_category_id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[parent_category_id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[parent_category_id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[parent_category_id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[parent_category_id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[applicability][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[applicability][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[applicability][ne]", in="query", required=false, @OA\Schema(type="string")),
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
     *          description="ProductCategory list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/ProductCategoryCollection")
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

        $paginated = new ProductCategoryCollection($serviceResult->data);
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
     *      path="/api/catalog/product-categories/{product_category}",
     *      tags={"ProductCategory"},
     *      summary="List a product category by ID",
     *      @OA\Parameter(
     *         name="product_category",
     *         in="path",
     *         required=true,
     *         description="ProductCategory ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="ProductCategory data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ProductCategoryResource"
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
    public function show(ProductCategory $product_category): JsonResponse {
        $serviceResult = $this->service->show($product_category);

        return $this->success(
            data: new ProductCategoryResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *      path="/api/catalog/product-categories",
     *      tags={"ProductCategory"},
     *      summary="Registers a product category",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for creating a new product category",
     *         @OA\JsonContent(ref="#/components/schemas/StoreProductCategoryRequest")
     *      ),
     *      @OA\Response(
     *          response="201", 
     *          description="Registered product category data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ProductCategoryResource"
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
    public function store(StoreProductCategoryRequest $request): JsonResponse {
        $dto = ProductCategoryDTO::fromArray($request->validated());
        $serviceResult = $this->service->store($dto);

        return $this->success(
            data: new ProductCategoryResource($serviceResult->data),
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Get(
     *      path="/api/catalog/product-categories/{product_category}/edit",
     *      tags={"ProductCategory"},
     *      summary="Get data to edit a product category",
     *      @OA\Parameter(
     *         name="product_category",
     *         in="path",
     *         required=true,
     *         description="ProductCategory ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="ProductCategory data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ProductCategoryResource"
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
    public function edit(ProductCategory $product_category): JsonResponse {
        $serviceResult = $this->service->edit($product_category);

        return $this->success(
            data: new ProductCategoryResource($serviceResult->data),
            warnings: $serviceResult->warnings,
            meta: $serviceResult->meta,
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Put(
     *      path="/api/catalog/product-categories/{product_category}",
     *      tags={"ProductCategory"},
     *      summary="Update a product category",
     *      @OA\Parameter(
     *         name="product_category",
     *         in="path",
     *         required=true,
     *         description="ProductCategory ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for update product category",
     *         @OA\JsonContent(ref="#/components/schemas/UpdateProductCategoryRequest")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Updated product category data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ProductCategoryResource"
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
    public function update(ProductCategory $product_category, UpdateProductCategoryRequest $request): JsonResponse {
        $dto = ProductCategoryDTO::fromArray($request->validated());
        $serviceResult = $this->service->update($product_category, $dto);

        return $this->success(
            data: new ProductCategoryResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/catalog/product-categories/{product_category}",
     *      tags={"ProductCategory"},
     *      summary="Delete a product category",
     *      @OA\Parameter(
     *         name="product_category",
     *         in="path",
     *         required=true,
     *         description="ProductCategory ID",
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
    public function destroy(ProductCategory $product_category): Response {
        $this->service->delete($product_category);
        return response()->noContent();
    }

    protected function exportModelClass(): string
    {
        return ProductCategory::class;
    }

    protected function exportClassForFormat(string $format): string
    {
        return match ($format) {
            'full' => ProductCategoryExport::class,
            default => throw new InvalidArgumentException(
                "Formato de exportação [{$format}] não suportado."
            ),
        };
    }/**
     * @OA\Get(
     *      path="/api/catalog/product-categories/lookup",
     *      tags={"ProductCategory"},
     *      summary="Lookup product categories",
     *      @OA\Parameter(ref="#/components/parameters/qParam"),
     *      @OA\Parameter(name="keys[]", in="query", description="Keys to find", required=false, @OA\Schema(type="{{lookup_key_type}}")),
     *      @OA\Parameter(ref="#/components/parameters/sortsParam"),
     *      @OA\Parameter(ref="#/components/parameters/perPageParam"),
     *      @OA\Parameter(ref="#/components/parameters/pageParam"),
     *      @OA\Response(
     *          response="200", 
     *          description="product category lookup list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/ProductCategoryLookupCollection")
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

        $paginated = new ProductCategoryLookupCollection($serviceResult->data);
        $paginated = $paginated->toArray($request);

        return $this->success(
            data: $paginated['data'],
            links: $paginated['links'],
            meta: $paginated['meta'],
            httpStatus: Response::HTTP_OK
        );
    }
}