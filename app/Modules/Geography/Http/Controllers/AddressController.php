<?php

namespace App\Modules\Geography\Http\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Core\Http\Requests\Core\ListRequest;
use App\Core\Traits\HasActivityLogs;
use App\Modules\Geography\DTO\AddressDTO;
use App\Modules\Geography\Http\Requests\Address\StoreAddressRequest;
use App\Modules\Geography\Http\Requests\Address\UpdateAddressRequest;
use App\Modules\Geography\Http\Resources\Address\AddressCollection;
use App\Modules\Geography\Http\Resources\Address\AddressResource;
use App\Modules\Geography\Models\Address;
use App\Modules\Geography\Services\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * @OA\PathItem(
 *     path="/api/geography/addresses/{id}/activity-logs",
 *
 *     @OA\Get(
 *         tags={"Address"},
 *         summary="List activity logs of an address",
 *         operationId="listAddressActivityLogs",
 *
 *         @OA\Parameter(
 *             name="id",
 *             in="path",
 *             required=true,
 *             description="Address ID",
 *
 *             @OA\Schema(type="integer")
 *         ),
 *
 *         @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
 *
 *         @OA\Response(
 *             response="200",
 *             description="Address activity logs",
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
 *         @OA\Response(
 *             response="401",
 *             description="Unauthorized",
 *
 *             @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
 *         ),
 *
 *         @OA\Response(
 *             response="403",
 *             description="Forbidden",
 *
 *             @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
 *         ),
 *
 *         @OA\Response(
 *             response="404",
 *             description="Record not found",
 *
 *             @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
 *         ),
 *         security={{"bearerAuth":{}}}
 *     )
 * )
 */
class AddressController extends Controller
{
    use HasActivityLogs;

    protected AddressService $service;

    public function __construct(AddressService $service)
    {
        $this->service = $service;
        $this->authorizeResource(Address::class, 'address');
    }

    protected function activityLogModelClass(): string
    {
        return Address::class;
    }

    /**
     * @OA\Get(
     *      path="/api/geography/addresses",
     *      tags={"Address"},
     *      summary="List all addresses",
     *
     *      @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[addressable_type][eq]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[addressable_id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[type][eq]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[city_name][like]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[created_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(ref="#/components/parameters/qParam"),
     *      @OA\Parameter(ref="#/components/parameters/sortsParam"),
     *      @OA\Parameter(ref="#/components/parameters/perPageParam"),
     *      @OA\Parameter(ref="#/components/parameters/pageParam"),
     *
     *      @OA\Response(
     *          response="200",
     *          description="Address list",
     *
     *          @OA\JsonContent(
     *              allOf={
     *
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/AddressCollection")
     *              }
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function index(ListRequest $request): JsonResponse
    {
        $serviceResult = $this->service->list($request->all());

        $paginated = new AddressCollection($serviceResult->data);
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
     *      path="/api/geography/addresses/{address}",
     *      tags={"Address"},
     *      summary="List an address by ID",
     *
     *      @OA\Parameter(
     *         name="address",
     *         in="path",
     *         required=true,
     *         description="Address ID",
     *
     *         @OA\Schema(type="integer")
     *      ),
     *
     *      @OA\Response(
     *          response="200",
     *          description="Address data",
     *
     *          @OA\JsonContent(
     *              allOf={
     *
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/AddressResource"
     *                      )
     *                  )
     *              }
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *
     *      @OA\Response(
     *          response="404",
     *          description="Record not found",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function show(Address $address): JsonResponse
    {
        $serviceResult = $this->service->show($address);

        return $this->success(
            data: new AddressResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *      path="/api/geography/addresses",
     *      tags={"Address"},
     *      summary="Registers an address",
     *
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for creating a new address",
     *
     *         @OA\JsonContent(ref="#/components/schemas/StoreAddressRequest")
     *      ),
     *
     *      @OA\Response(
     *          response="201",
     *          description="Registered address data",
     *
     *          @OA\JsonContent(
     *              allOf={
     *
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/AddressResource"
     *                      )
     *                  )
     *              }
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *
     *      @OA\Response(
     *          response="422",
     *          description="Unprocessable Entity",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function store(StoreAddressRequest $request): JsonResponse
    {
        $dto = AddressDTO::fromArray($request->validated());
        $serviceResult = $this->service->store($dto);

        return $this->success(
            data: new AddressResource($serviceResult->data),
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Get(
     *      path="/api/geography/addresses/{address}/edit",
     *      tags={"Address"},
     *      summary="Get data to edit an address",
     *
     *      @OA\Parameter(
     *         name="address",
     *         in="path",
     *         required=true,
     *         description="Address ID",
     *
     *         @OA\Schema(type="integer")
     *      ),
     *
     *      @OA\Response(
     *          response="200",
     *          description="Address data",
     *
     *          @OA\JsonContent(
     *              allOf={
     *
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/AddressResource"
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
     *
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *
     *      @OA\Response(
     *          response="404",
     *          description="Record not found",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      )
     * )
     */
    public function edit(Address $address): JsonResponse
    {
        $serviceResult = $this->service->edit($address);

        return $this->success(
            data: new AddressResource($serviceResult->data),
            warnings: $serviceResult->warnings,
            meta: $serviceResult->meta,
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Put(
     *      path="/api/geography/addresses/{address}",
     *      tags={"Address"},
     *      summary="Update an address",
     *
     *      @OA\Parameter(
     *         name="address",
     *         in="path",
     *         required=true,
     *         description="Address ID",
     *
     *         @OA\Schema(type="integer")
     *      ),
     *
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for update address",
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateAddressRequest")
     *      ),
     *
     *      @OA\Response(
     *          response="200",
     *          description="Updated address data",
     *
     *          @OA\JsonContent(
     *              allOf={
     *
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/AddressResource"
     *                      )
     *                  )
     *              }
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *
     *      @OA\Response(
     *          response="404",
     *          description="Record not found",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *
     *      @OA\Response(
     *          response="422",
     *          description="Unprocessable Entity",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function update(Address $address, UpdateAddressRequest $request): JsonResponse
    {
        $data = array_merge([
            'addressable_type' => $address->addressable_type,
            'addressable_id' => $address->addressable_id,
        ], $request->validated());

        $dto = AddressDTO::fromArray($data);
        $serviceResult = $this->service->update($address, $dto);

        return $this->success(
            data: new AddressResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/geography/addresses/{address}",
     *      tags={"Address"},
     *      summary="Delete an address",
     *
     *      @OA\Parameter(
     *         name="address",
     *         in="path",
     *         required=true,
     *         description="Address ID",
     *
     *         @OA\Schema(type="integer")
     *      ),
     *
     *      @OA\Response(
     *          response="204",
     *          description="No content"
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *
     *      @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *
     *      @OA\Response(
     *          response="404",
     *          description="Record not found",
     *
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(Address $address): Response
    {
        $this->service->delete($address);

        return response()->noContent();
    }
}
