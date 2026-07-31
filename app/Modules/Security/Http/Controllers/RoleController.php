<?php

namespace App\Modules\Security\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Core\Http\Controllers\Controller;
use App\Core\Http\Requests\Core\ListRequest;
use App\Core\Http\Requests\Core\LookupRequest;
use App\Modules\Security\Http\Requests\Role\StoreRoleRequest;
use App\Modules\Security\Http\Requests\Role\UpdateRoleRequest;
use App\Modules\Security\Http\Resources\Role\RoleResource;
use App\Modules\Security\Http\Resources\Role\RoleCollection;
use App\Modules\Security\DTO\RoleDTO;
use App\Modules\Security\Http\Requests\Role\RolePermissionsRequest;
use App\Modules\Security\Http\Resources\Role\RoleLookupCollection;
use App\Modules\Security\Models\Role;
use App\Modules\Security\Services\RoleService;

class RoleController extends Controller
{
    protected RoleService $service;

    public function __construct(RoleService $service) {
        $this->service = $service;
        $this->authorizeResource(Role::class, 'role');
        $this->middleware('can:definePermissions,role')->only(['definePermissions']);
    }

    /**
     * @OA\Get(
     *      path="/api/security/roles",
     *      tags={"Role"},
     *      summary="List all rows",
     *      @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filters[name][like]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[description][like]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[created_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="filters[updated_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(ref="#/components/parameters/sortsParam"),
     *      @OA\Parameter(ref="#/components/parameters/perPageParam"),
     *      @OA\Parameter(ref="#/components/parameters/pageParam"),
     *      @OA\Response(
     *          response="200", 
     *          description="Role list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/RoleCollection")
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

        $paginated = new RoleCollection($serviceResult->data);
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
     *      path="/api/core/roles/{id}",
     *      tags={"Role"},
     *      summary="List a role by ID",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Role data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/RoleResource"
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
    public function show(Role $role): JsonResponse {
        $serviceResult = $this->service->show($role);

        return $this->success(
            data: new RoleResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *      path="/api/core/roles",
     *      tags={"Role"},
     *      summary="Registers a role",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for creating a new role",
     *         @OA\JsonContent(ref="#/components/schemas/StoreRoleRequest")
     *      ),
     *      @OA\Response(
     *          response="201", 
     *          description="Registered role data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/RoleResource"
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
    public function store(StoreRoleRequest $request): JsonResponse {
        $dto = RoleDTO::fromArray($request->validated());
        $serviceResult = $this->service->store($dto);

        return $this->success(
            data: new RoleResource($serviceResult->data),
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Get(
     *      path="/api/core/roles/{id}/edit",
     *      tags={"Role"},
     *      summary="Get data to edit a role",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Role data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/RoleResource"
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
    public function edit(Role $role): JsonResponse {
        $serviceResult = $this->service->edit($role);

        return $this->success(
            data: new RoleResource($serviceResult->data),
            warnings: $serviceResult->warnings,
            meta: $serviceResult->meta,
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Put(
     *      path="/api/core/roles/{id}",
     *      tags={"Role"},
     *      summary="Update a role",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for update role",
     *         @OA\JsonContent(ref="#/components/schemas/UpdateRoleRequest")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Updated role data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/RoleResource"
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
    public function update(Role $role, UpdateRoleRequest $request): JsonResponse {
        $dto = RoleDTO::fromArray($request->validated());
        $serviceResult = $this->service->update($role, $dto);

        return $this->success(
            data: new RoleResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/core/roles/{id}",
     *      tags={"Role"},
     *      summary="Delete a role",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
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
    public function destroy(Role $role): Response {
        $this->service->delete($role);
        return response()->noContent();
    }

    /**
     * @OA\Patch(
     *      path="/api/security/roles/{id}/permissions",
     *      tags={"Role"},
     *      summary="Define role permissions",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for set role",
     *         @OA\JsonContent(ref="#/components/schemas/RolePermissionsRequest")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Updated role data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/RoleResource"
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
    public function definePermissions(Role $role, RolePermissionsRequest $request): JsonResponse {
        $serviceResult = $this->service->definePermissions($role, $request->validated('ids'));

        return $this->success(
            data: new RoleResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    public function lookup(LookupRequest $request): JsonResponse {
        $serviceResult = $this->service->lookup($request->all());

        $paginated = new RoleLookupCollection($serviceResult->data);
        $paginated = $paginated->toArray($request);

        return $this->success(
            data: $paginated['data'],
            links: $paginated['links'],
            meta: $paginated['meta'],
            httpStatus: Response::HTTP_OK
        );
    }
}