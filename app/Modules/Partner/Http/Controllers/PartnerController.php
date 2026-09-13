<?php

namespace App\Modules\Partner\Http\Controllers;


use App\Core\Http\Requests\Core\LookupRequest;
use App\Modules\Partner\Http\Resources\Partner\PartnerLookupCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use App\Core\Http\Controllers\Controller;
use App\Core\Http\Requests\Core\ListRequest;
use App\Modules\Partner\Http\Requests\Partner\StorePartnerRequest;
use App\Modules\Partner\Http\Requests\Partner\UpdatePartnerRequest;
use App\Modules\Partner\Http\Resources\Partner\PartnerResource;
use App\Modules\Partner\Http\Resources\Partner\PartnerCollection;
use App\Modules\Partner\DTO\PartnerDTO;
use App\Modules\Partner\Models\Partner;
use App\Modules\Partner\Services\PartnerService;
use App\Core\Traits\HasActivityLogs;

/**
 * @OA\PathItem(
 *     path="/api/partner/partners/{id}/activity-logs",
 *     @OA\Get(
 *         tags={"Partner"},
 *         summary="List activity logs of a partner",
 *         @OA\Parameter(
 *             name="id",
 *             in="path",
 *             required=true,
 *             description="Partner ID",
 *             @OA\Schema(type="integer")
 *         ),
 *         @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Response(
 *             response="200",
 *             description="Partner activity logs",
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
 */
class PartnerController extends Controller
{
    use HasActivityLogs;

    protected PartnerService $service;

    public function __construct(PartnerService $service) {
        $this->service = $service;
        $this->authorizeResource(Partner::class, 'partner');
    }

    protected function activityLogModelClass(): string
    {
        return Partner::class;
    }

    /**
     * @OA\Get(
     *      path="/api/partner/partners",
     *      tags={"Partner"},
     *      summary="List all rows",
     *      @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[name][like]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[created_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[updated_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(ref="#/components/parameters/sortsParam"),
     *      @OA\Parameter(ref="#/components/parameters/perPageParam"),
     *      @OA\Parameter(ref="#/components/parameters/pageParam"),
     *      @OA\Response(
     *          response="200", 
     *          description="Partner list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/PartnerCollection")
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

        $paginated = new PartnerCollection($serviceResult->data);
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
     *      path="/api/partner/partners/{id}",
     *      tags={"Partner"},
     *      summary="List a partner by ID",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Partner ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Partner data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/PartnerResource"
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
    public function show(Partner $partner): JsonResponse {
        $serviceResult = $this->service->show($partner);

        return $this->success(
            data: new PartnerResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *      path="/api/partner/partners",
     *      tags={"Partner"},
     *      summary="Registers a partner",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for creating a new partner",
     *         @OA\JsonContent(ref="#/components/schemas/StorePartnerRequest")
     *      ),
     *      @OA\Response(
     *          response="201", 
     *          description="Registered partner data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/PartnerResource"
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
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function store(StorePartnerRequest $request): JsonResponse {
        $dto = PartnerDTO::fromArray($request->validated());
        $serviceResult = $this->service->store($dto);

        return $this->success(
            data: new PartnerResource($serviceResult->data),
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Get(
     *      path="/api/partner/partners/{id}/edit",
     *      tags={"Partner"},
     *      summary="Get data to edit a partner",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Partner ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Partner data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/PartnerResource"
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
    public function edit(Partner $partner): JsonResponse {
        $serviceResult = $this->service->edit($partner);

        return $this->success(
            data: new PartnerResource($serviceResult->data),
            warnings: $serviceResult->warnings,
            meta: $serviceResult->meta,
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Put(
     *      path="/api/partner/partners/{id}",
     *      tags={"Partner"},
     *      summary="Update a partner",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Partner ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for update partner",
     *         @OA\JsonContent(ref="#/components/schemas/UpdatePartnerRequest")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Updated partner data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/PartnerResource"
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
    public function update(Partner $partner, UpdatePartnerRequest $request): JsonResponse {
        $dto = PartnerDTO::fromArray($request->validated());
        $serviceResult = $this->service->update($partner, $dto);

        return $this->success(
            data: new PartnerResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/partner/partners/{id}",
     *      tags={"Partner"},
     *      summary="Delete a partner",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Partner ID",
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
    public function destroy(Partner $partner): Response {
        $this->service->delete($partner);
        return response()->noContent();
    }

    /**
     * @OA\Get(
     *      path="/api/partner/partners/lookup",
     *      tags={"Partner"},
     *      summary="Lookup partners",
     *      @OA\Parameter(name="q", in="query", description="Query for avaiable fields", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="keys[]", in="query", description="Keys to find", required=false, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response="200", 
     *          description="Partner type lookup list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/PartnerLookupCollection")
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

        $paginated = new PartnerLookupCollection($serviceResult->data);
        $paginated = $paginated->toArray($request);

        return $this->success(
            data: $paginated['data'],
            links: $paginated['links'],
            meta: $paginated['meta'],
            httpStatus: Response::HTTP_OK
        );
    }
}