<?php

namespace App\Modules\Security\Http\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Core\Http\Requests\Core\ListRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Modules\Security\Http\Requests\Permission\StorePermissionRequest;
use App\Modules\Security\Http\Requests\Permission\UpdatePermissionRequest;
use App\Modules\Security\Http\Resources\Permission\PermissionResource;
use App\Modules\Security\Http\Resources\Permission\PermissionCollection;
use App\Modules\Security\DTO\PermissionDTO;
use App\Modules\Security\Models\Permission;
use App\Modules\Security\Services\PermissionService;

class PermissionController extends Controller
{
    protected PermissionService $service;

    public function __construct(PermissionService $service) {
        $this->service = $service;
        $this->authorizeResource(Permission::class, 'permission');
    }

    /**
     * @OA\Get(
     *      path="/api/security/permissions",
     *      tags={"Permission"},
     *      summary="List all rows",
     *      @OA\Parameter(name="id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="module_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="resource", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="action", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="name", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="description", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="created_at", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="updated_at", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="deleted_at", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="sort_by[]", in="query", description="Fields name's to sort", required=false, @OA\Schema(type="array", @OA\Items(type="string", example="id"))),
     *      @OA\Parameter(name="sort_dir[]", in="query", description="Directions to sort: Ascending = 'asc'; Descending = 'desc'", required=false, @OA\Schema(type="array", @OA\Items(type="string", example="desc"))),
     *      @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response="200", 
     *          description="Permission list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/PermissionCollection")
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

        $paginated = new PermissionCollection($serviceResult->data);
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
     *      path="/api/core/permissions/{id}",
     *      tags={"Permission"},
     *      summary="List a permission by ID",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Permission ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Permission data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/PermissionResource"
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
    public function show(Permission $permission): JsonResponse {
        $serviceResult = $this->service->show($permission);

        return $this->success(
            data: new PermissionResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *      path="/api/core/permissions",
     *      tags={"Permission"},
     *      summary="Registers a permission",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for creating a new permission",
     *         @OA\JsonContent(ref="#/components/schemas/StorePermissionRequest")
     *      ),
     *      @OA\Response(
     *          response="201", 
     *          description="Registered permission data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/PermissionResource"
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
    public function store(StorePermissionRequest $request): JsonResponse {
        $dto = PermissionDTO::fromArray($request->validated());
        $serviceResult = $this->service->store($dto);

        return $this->success(
            data: new PermissionResource($serviceResult->data),
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Get(
     *      path="/api/core/permissions/{id}/edit",
     *      tags={"Permission"},
     *      summary="Get data to edit a permission",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Permission ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Permission data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/PermissionResource"
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
    public function edit(Permission $permission): JsonResponse {
        $serviceResult = $this->service->edit($permission);

        return $this->success(
            data: new PermissionResource($serviceResult->data),
            warnings: $serviceResult->warnings,
            meta: $serviceResult->meta,
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Put(
     *      path="/api/core/permissions/{id}",
     *      tags={"Permission"},
     *      summary="Update a permission",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Permission ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for update permission",
     *         @OA\JsonContent(ref="#/components/schemas/UpdatePermissionRequest")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Updated permission data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/PermissionResource"
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
    public function update(Permission $permission, UpdatePermissionRequest $request): JsonResponse {
        $dto = PermissionDTO::fromArray($request->validated());
        $serviceResult = $this->service->update($permission, $dto);

        return $this->success(
            data: new PermissionResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/core/permissions/{id}",
     *      tags={"Permission"},
     *      summary="Delete a permission",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Permission ID",
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
    public function destroy(Permission $permission): Response {
        $this->service->delete($permission);
        return response()->noContent();
    }
}