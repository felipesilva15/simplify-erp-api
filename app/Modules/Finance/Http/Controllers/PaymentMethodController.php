<?php

namespace App\Modules\Finance\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Core\Http\Requests\Core\LookupRequest;
use App\Modules\Finance\Http\Resources\PaymentMethod\PaymentMethodLookupCollection;

use App\Core\Http\Controllers\Controller;
use App\Core\Http\Requests\Core\ListRequest;
use App\Modules\Finance\Http\Requests\PaymentMethod\StorePaymentMethodRequest;
use App\Modules\Finance\Http\Requests\PaymentMethod\UpdatePaymentMethodRequest;
use App\Modules\Finance\Http\Resources\PaymentMethod\PaymentMethodResource;
use App\Modules\Finance\Http\Resources\PaymentMethod\PaymentMethodCollection;
use App\Modules\Finance\DTO\PaymentMethodDTO;
use App\Modules\Finance\Models\PaymentMethod;
use App\Modules\Finance\Services\PaymentMethodService;
use App\Core\Traits\HasActivityLogs;
use App\Core\Traits\HasExcelExport;
use App\Modules\Finance\Exports\PaymentMethodExport;
use InvalidArgumentException;

/**
 * @OA\PathItem(
 *     path="/api/finance/payment-methods/{id}/activity-logs",
 *     @OA\Get(
 *         tags={"PaymentMethod"},
 *         summary="List activity logs of a payment method",
 *         operationId="listPaymentMethodActivityLogs",
 *         @OA\Parameter(
 *             name="id",
 *             in="path",
 *             required=true,
 *             description="PaymentMethod ID",
 *             @OA\Schema(type="integer")
 *         ),
 *         @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Response(
 *             response="200",
 *             description="PaymentMethod activity logs",
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
 *     path="/api/finance/payment-methods/export",
 *     @OA\Get(
 *         tags={"PaymentMethod"},
 *         summary="Export all payment methods to Excel",
 *         operationId="exportPaymentMethod",
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
     *     @OA\Parameter(name="filters[condition][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[condition][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[condition][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[payment_type][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[payment_type][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[payment_type][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[applicable_type][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[applicable_type][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[applicable_type][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[tax_type][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[tax_type][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[tax_type][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[tax_aliquot][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_aliquot][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_aliquot][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_aliquot][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_aliquot][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_aliquot][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_fixed_value][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_fixed_value][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_fixed_value][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_fixed_value][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_fixed_value][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_fixed_value][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[penalty_aliquot][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[penalty_aliquot][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[penalty_aliquot][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[penalty_aliquot][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[penalty_aliquot][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[penalty_aliquot][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[interest_aliquot][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[interest_aliquot][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[interest_aliquot][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[interest_aliquot][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[interest_aliquot][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[interest_aliquot][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[payment_deadline][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[payment_deadline][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[payment_deadline][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[payment_deadline][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[payment_deadline][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[payment_deadline][ne]", in="query", required=false, @OA\Schema(type="integer")),
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
 *             description="Excel file with payment methods",
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
class PaymentMethodController extends Controller
{
    use HasActivityLogs;
    use HasExcelExport;

    protected PaymentMethodService $service;

    public function __construct(PaymentMethodService $service) {
        $this->service = $service;
        $this->authorizeResource(PaymentMethod::class, 'payment_method');
    }

    protected function activityLogModelClass(): string
    {
        return PaymentMethod::class;
    }

    /**
     * @OA\Get(
     *      path="/api/finance/payment-methods",
     *      tags={"PaymentMethod"},
     *      summary="List all payment methods",
     *     @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[name][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[condition][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[condition][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[condition][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[payment_type][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[payment_type][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[payment_type][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[applicable_type][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[applicable_type][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[applicable_type][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[tax_type][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[tax_type][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[tax_type][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[tax_aliquot][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_aliquot][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_aliquot][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_aliquot][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_aliquot][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_aliquot][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_fixed_value][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_fixed_value][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_fixed_value][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_fixed_value][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_fixed_value][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[tax_fixed_value][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[penalty_aliquot][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[penalty_aliquot][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[penalty_aliquot][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[penalty_aliquot][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[penalty_aliquot][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[penalty_aliquot][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[interest_aliquot][eq]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[interest_aliquot][lt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[interest_aliquot][lte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[interest_aliquot][gt]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[interest_aliquot][gte]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[interest_aliquot][ne]", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="filters[payment_deadline][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[payment_deadline][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[payment_deadline][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[payment_deadline][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[payment_deadline][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[payment_deadline][ne]", in="query", required=false, @OA\Schema(type="integer")),
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
     *          description="PaymentMethod list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/PaymentMethodCollection")
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

        $paginated = new PaymentMethodCollection($serviceResult->data);
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
     *      path="/api/finance/payment-methods/{payment_method}",
     *      tags={"PaymentMethod"},
     *      summary="List a payment method by ID",
     *      @OA\Parameter(
     *         name="payment_method",
     *         in="path",
     *         required=true,
     *         description="PaymentMethod ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="PaymentMethod data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/PaymentMethodResource"
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
    public function show(PaymentMethod $payment_method): JsonResponse {
        $serviceResult = $this->service->show($payment_method);

        return $this->success(
            data: new PaymentMethodResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *      path="/api/finance/payment-methods",
     *      tags={"PaymentMethod"},
     *      summary="Registers a payment method",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for creating a new payment method",
     *         @OA\JsonContent(ref="#/components/schemas/StorePaymentMethodRequest")
     *      ),
     *      @OA\Response(
     *          response="201", 
     *          description="Registered payment method data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/PaymentMethodResource"
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
    public function store(StorePaymentMethodRequest $request): JsonResponse {
        $dto = PaymentMethodDTO::fromArray($request->validated());
        $serviceResult = $this->service->store($dto);

        return $this->success(
            data: new PaymentMethodResource($serviceResult->data),
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Get(
     *      path="/api/finance/payment-methods/{payment_method}/edit",
     *      tags={"PaymentMethod"},
     *      summary="Get data to edit a payment method",
     *      @OA\Parameter(
     *         name="payment_method",
     *         in="path",
     *         required=true,
     *         description="PaymentMethod ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="PaymentMethod data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/PaymentMethodResource"
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
    public function edit(PaymentMethod $payment_method): JsonResponse {
        $serviceResult = $this->service->edit($payment_method);

        return $this->success(
            data: new PaymentMethodResource($serviceResult->data),
            warnings: $serviceResult->warnings,
            meta: $serviceResult->meta,
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Put(
     *      path="/api/finance/payment-methods/{payment_method}",
     *      tags={"PaymentMethod"},
     *      summary="Update a payment method",
     *      @OA\Parameter(
     *         name="payment_method",
     *         in="path",
     *         required=true,
     *         description="PaymentMethod ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for update payment method",
     *         @OA\JsonContent(ref="#/components/schemas/UpdatePaymentMethodRequest")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Updated payment method data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/PaymentMethodResource"
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
    public function update(PaymentMethod $payment_method, UpdatePaymentMethodRequest $request): JsonResponse {
        $dto = PaymentMethodDTO::fromArray($request->validated());
        $serviceResult = $this->service->update($payment_method, $dto);

        return $this->success(
            data: new PaymentMethodResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/finance/payment-methods/{payment_method}",
     *      tags={"PaymentMethod"},
     *      summary="Delete a payment method",
     *      @OA\Parameter(
     *         name="payment_method",
     *         in="path",
     *         required=true,
     *         description="PaymentMethod ID",
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
    public function destroy(PaymentMethod $payment_method): Response {
        $this->service->delete($payment_method);
        return response()->noContent();
    }

    protected function exportModelClass(): string
    {
        return PaymentMethod::class;
    }

    protected function exportClassForFormat(string $format): string
    {
        return match ($format) {
            'full' => PaymentMethodExport::class,
            default => throw new InvalidArgumentException(
                "Formato de exportação [{$format}] não suportado."
            ),
        };
    }/**
     * @OA\Get(
     *      path="/api/finance/payment-methods/lookup",
     *      tags={"PaymentMethod"},
     *      summary="Lookup payment methods",
     *      @OA\Parameter(ref="#/components/parameters/qParam"),
     *      @OA\Parameter(name="keys[]", in="query", description="Keys to find", required=false, @OA\Schema(type="{{lookup_key_type}}")),
     *      @OA\Parameter(ref="#/components/parameters/sortsParam"),
     *      @OA\Parameter(ref="#/components/parameters/perPageParam"),
     *      @OA\Parameter(ref="#/components/parameters/pageParam"),
     *      @OA\Response(
     *          response="200", 
     *          description="payment method lookup list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/PaymentMethodLookupCollection")
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

        $paginated = new PaymentMethodLookupCollection($serviceResult->data);
        $paginated = $paginated->toArray($request);

        return $this->success(
            data: $paginated['data'],
            links: $paginated['links'],
            meta: $paginated['meta'],
            httpStatus: Response::HTTP_OK
        );
    }
}