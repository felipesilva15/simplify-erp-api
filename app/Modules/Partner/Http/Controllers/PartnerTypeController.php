<?php

namespace App\Modules\Partner\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use App\Core\Http\Controllers\Controller;
use App\Core\Http\Requests\Core\ListRequest;
use App\Modules\Partner\Http\Requests\PartnerType\StorePartnerTypeRequest;
use App\Modules\Partner\Http\Requests\PartnerType\UpdatePartnerTypeRequest;
use App\Modules\Partner\Http\Resources\PartnerType\PartnerTypeResource;
use App\Modules\Partner\Http\Resources\PartnerType\PartnerTypeCollection;
use App\Modules\Partner\DTO\PartnerTypeDTO;
use App\Modules\Partner\Models\PartnerType;
use App\Modules\Partner\Services\PartnerTypeService;

class PartnerTypeController extends Controller
{
    protected PartnerTypeService $service;

    public function __construct(PartnerTypeService $service) {
        $this->service = $service;
        $this->authorizeResource(PartnerType::class, 'partner_type');
    }

    /**
     * @OA\Get(
     *      path="/api/partner/partner_types",
     *      tags={"PartnerType"},
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
     *          description="PartnerType list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/PartnerTypeCollection")
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

        $paginated = new PartnerTypeCollection($serviceResult->data);
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
     *      path="/api/partner/partner_types/{id}",
     *      tags={"PartnerType"},
     *      summary="List a partner_type by ID",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="PartnerType ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="PartnerType data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/PartnerTypeResource"
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
    public function show(PartnerType $partner_type): JsonResponse {
        $serviceResult = $this->service->show($partner_type);

        return $this->success(
            data: new PartnerTypeResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *      path="/api/partner/partner_types",
     *      tags={"PartnerType"},
     *      summary="Registers a partner_type",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for creating a new partner_type",
     *         @OA\JsonContent(ref="#/components/schemas/StorePartnerTypeRequest")
     *      ),
     *      @OA\Response(
     *          response="201", 
     *          description="Registered partner_type data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/PartnerTypeResource"
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
    public function store(StorePartnerTypeRequest $request): JsonResponse {
        $dto = PartnerTypeDTO::fromArray($request->validated());
        $serviceResult = $this->service->store($dto);

        return $this->success(
            data: new PartnerTypeResource($serviceResult->data),
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Get(
     *      path="/api/partner/partner_types/{id}/edit",
     *      tags={"PartnerType"},
     *      summary="Get data to edit a partner_type",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="PartnerType ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="PartnerType data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/PartnerTypeResource"
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
    public function edit(PartnerType $partner_type): JsonResponse {
        $serviceResult = $this->service->edit($partner_type);

        return $this->success(
            data: new PartnerTypeResource($serviceResult->data),
            warnings: $serviceResult->warnings,
            meta: $serviceResult->meta,
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Put(
     *      path="/api/partner/partner_types/{id}",
     *      tags={"PartnerType"},
     *      summary="Update a partner_type",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="PartnerType ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for update partner_type",
     *         @OA\JsonContent(ref="#/components/schemas/UpdatePartnerTypeRequest")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Updated partner_type data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/PartnerTypeResource"
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
    public function update(PartnerType $partner_type, UpdatePartnerTypeRequest $request): JsonResponse {
        $dto = PartnerTypeDTO::fromArray($request->validated());
        $serviceResult = $this->service->update($partner_type, $dto);

        return $this->success(
            data: new PartnerTypeResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/partner/partner_types/{id}",
     *      tags={"PartnerType"},
     *      summary="Delete a partner_type",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="PartnerType ID",
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
    public function destroy(PartnerType $partner_type): Response {
        $this->service->delete($partner_type);
        return response()->noContent();
    }
}