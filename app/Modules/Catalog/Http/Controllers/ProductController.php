<?php

namespace App\Modules\Catalog\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Core\Http\Requests\Core\LookupRequest;
use App\Modules\Catalog\Http\Resources\Product\ProductLookupCollection;

use App\Core\Http\Controllers\Controller;
use App\Core\Http\Requests\Core\ListRequest;
use App\Modules\Catalog\Http\Requests\Product\StoreProductRequest;
use App\Modules\Catalog\Http\Requests\Product\UpdateProductRequest;
use App\Modules\Catalog\Http\Resources\Product\ProductResource;
use App\Modules\Catalog\Http\Resources\Product\ProductCollection;
use App\Modules\Catalog\DTO\ProductDTO;
use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Services\ProductService;
use App\Core\Traits\HasActivityLogs;
use App\Core\Traits\HasExcelExport;
use App\Modules\Catalog\Exports\ProductExport;
use InvalidArgumentException;

/**
 * @OA\PathItem(
 *     path="/api/catalog/products/{id}/activity-logs",
 *     @OA\Get(
 *         tags={"Product"},
 *         summary="List activity logs of a product",
 *         operationId="listProductActivityLogs",
 *         @OA\Parameter(
 *             name="id",
 *             in="path",
 *             required=true,
 *             description="Product ID",
 *             @OA\Schema(type="integer")
 *         ),
 *         @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Response(
 *             response="200",
 *             description="Product activity logs",
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
 *     path="/api/catalog/products/export",
 *     @OA\Get(
 *         tags={"Product"},
 *         summary="Export all products to Excel",
 *         operationId="exportProduct",
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
     *     @OA\Parameter(name="filters[type][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[type][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[type][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[item_type_code][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[item_type_code][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[item_type_code][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[sku][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[sku][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[sku][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[gtin][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[gtin][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[gtin][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[description][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[description][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[description][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[notes][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[notes][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[notes][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[price][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[price][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[price][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[price][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[price][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[price][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[cost][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[cost][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[cost][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[cost][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[cost][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[cost][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[net_weight][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[net_weight][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[net_weight][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[net_weight][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[net_weight][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[net_weight][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[gross_weight][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[gross_weight][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[gross_weight][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[gross_weight][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[gross_weight][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[gross_weight][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[width][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[width][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[width][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[width][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[width][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[width][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[height][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[height][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[height][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[height][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[height][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[height][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[length][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[length][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[length][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[length][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[length][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[length][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[minimum_stock][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[minimum_stock][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[minimum_stock][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[minimum_stock][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[minimum_stock][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[minimum_stock][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[maximum_stock][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[maximum_stock][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[maximum_stock][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[maximum_stock][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[maximum_stock][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[maximum_stock][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[is_active][eq]", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="filters[is_active][ne]", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="filters[measurement_unit_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[measurement_unit_id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[measurement_unit_id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[measurement_unit_id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[measurement_unit_id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[measurement_unit_id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_category_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_category_id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_category_id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_category_id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_category_id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_category_id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_brand_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_brand_id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_brand_id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_brand_id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_brand_id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_brand_id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[deleted_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][ne]", in="query", required=false, @OA\Schema(type="string")),
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
 *         @OA\Response(
 *             response="200",
 *             description="Excel file with products",
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
class ProductController extends Controller
{
    use HasActivityLogs;
    use HasExcelExport;

    protected ProductService $service;

    public function __construct(ProductService $service) {
        $this->service = $service;
        $this->authorizeResource(Product::class, 'product');
    }

    protected function activityLogModelClass(): string
    {
        return Product::class;
    }

    /**
     * @OA\Get(
     *      path="/api/catalog/products",
     *      tags={"Product"},
     *      summary="List all products",
     *     @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[type][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[type][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[type][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[item_type_code][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[item_type_code][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[item_type_code][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[sku][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[sku][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[sku][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[gtin][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[gtin][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[gtin][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[description][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[description][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[description][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[notes][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[notes][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[notes][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[price][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[price][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[price][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[price][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[price][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[price][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[cost][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[cost][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[cost][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[cost][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[cost][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[cost][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[net_weight][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[net_weight][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[net_weight][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[net_weight][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[net_weight][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[net_weight][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[gross_weight][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[gross_weight][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[gross_weight][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[gross_weight][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[gross_weight][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[gross_weight][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[width][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[width][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[width][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[width][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[width][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[width][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[height][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[height][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[height][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[height][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[height][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[height][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[length][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[length][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[length][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[length][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[length][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[length][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[minimum_stock][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[minimum_stock][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[minimum_stock][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[minimum_stock][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[minimum_stock][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[minimum_stock][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[maximum_stock][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[maximum_stock][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[maximum_stock][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[maximum_stock][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[maximum_stock][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[maximum_stock][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[is_active][eq]", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="filters[is_active][ne]", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="filters[measurement_unit_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[measurement_unit_id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[measurement_unit_id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[measurement_unit_id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[measurement_unit_id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[measurement_unit_id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_category_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_category_id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_category_id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_category_id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_category_id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_category_id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_brand_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_brand_id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_brand_id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_brand_id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_brand_id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[product_brand_id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[deleted_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][ne]", in="query", required=false, @OA\Schema(type="string")),
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
     *     @OA\Parameter(ref="#/components/parameters/qParam"),
     *     @OA\Parameter(ref="#/components/parameters/sortsParam"),
     *     @OA\Parameter(ref="#/components/parameters/perPageParam"),
     *     @OA\Parameter(ref="#/components/parameters/pageParam"),
     *      @OA\Response(
     *          response="200", 
     *          description="Product list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/ProductCollection")
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

        $paginated = new ProductCollection($serviceResult->data);
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
     *      path="/api/catalog/products/{product}",
     *      tags={"Product"},
     *      summary="List a product by ID",
     *      @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Product data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ProductResource"
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
    public function show(Product $product): JsonResponse {
        $serviceResult = $this->service->show($product);

        return $this->success(
            data: new ProductResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *      path="/api/catalog/products",
     *      tags={"Product"},
     *      summary="Registers a product",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for creating a new product",
     *         @OA\JsonContent(ref="#/components/schemas/StoreProductRequest")
     *      ),
     *      @OA\Response(
     *          response="201", 
     *          description="Registered product data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ProductResource"
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
    public function store(StoreProductRequest $request): JsonResponse {
        $dto = ProductDTO::fromArray($request->validated());
        $serviceResult = $this->service->store($dto);

        return $this->success(
            data: new ProductResource($serviceResult->data),
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Get(
     *      path="/api/catalog/products/{product}/edit",
     *      tags={"Product"},
     *      summary="Get data to edit a product",
     *      @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Product data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ProductResource"
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
    public function edit(Product $product): JsonResponse {
        $serviceResult = $this->service->edit($product);

        return $this->success(
            data: new ProductResource($serviceResult->data),
            warnings: $serviceResult->warnings,
            meta: $serviceResult->meta,
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Put(
     *      path="/api/catalog/products/{product}",
     *      tags={"Product"},
     *      summary="Update a product",
     *      @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for update product",
     *         @OA\JsonContent(ref="#/components/schemas/UpdateProductRequest")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Updated product data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ProductResource"
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
    public function update(Product $product, UpdateProductRequest $request): JsonResponse {
        $dto = ProductDTO::fromArray($request->validated());
        $serviceResult = $this->service->update($product, $dto);

        return $this->success(
            data: new ProductResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/catalog/products/{product}",
     *      tags={"Product"},
     *      summary="Delete a product",
     *      @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
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
    public function destroy(Product $product): Response {
        $this->service->delete($product);
        return response()->noContent();
    }

    protected function exportModelClass(): string
    {
        return Product::class;
    }

    protected function exportClassForFormat(string $format): string
    {
        return match ($format) {
            'full' => ProductExport::class,
            default => throw new InvalidArgumentException(
                "Formato de exportação [{$format}] não suportado."
            ),
        };
    }/**
     * @OA\Get(
     *      path="/api/catalog/products/lookup",
     *      tags={"Product"},
     *      summary="Lookup products",
     *      @OA\Parameter(ref="#/components/parameters/qParam"),
     *      @OA\Parameter(name="keys[]", in="query", description="Keys to find", required=false, @OA\Schema(type="{{lookup_key_type}}")),
     *      @OA\Parameter(ref="#/components/parameters/sortsParam"),
     *      @OA\Parameter(ref="#/components/parameters/perPageParam"),
     *      @OA\Parameter(ref="#/components/parameters/pageParam"),
     *      @OA\Response(
     *          response="200", 
     *          description="product lookup list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/ProductLookupCollection")
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

        $paginated = new ProductLookupCollection($serviceResult->data);
        $paginated = $paginated->toArray($request);

        return $this->success(
            data: $paginated['data'],
            links: $paginated['links'],
            meta: $paginated['meta'],
            httpStatus: Response::HTTP_OK
        );
    }
}