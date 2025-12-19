<?php

namespace App\Modules\Security\Http\Controllers;

use App\Modules\Security\Actions\Role\DefineRolePermissionsAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use App\Core\Http\Controllers\Controller;
use App\Modules\Security\Actions\Role\StoreRoleAction;
use App\Modules\Security\Actions\Role\UpdateRoleAction;
use App\Modules\Security\Actions\Role\DeleteRoleAction;
use App\Modules\Security\Actions\Role\EditRoleAction;
use App\Modules\Security\Actions\Role\ShowRoleAction;
use App\Modules\Security\Actions\Role\ListRoleAction;
use App\Modules\Security\Http\Requests\Role\StoreRoleRequest;
use App\Modules\Security\Http\Requests\Role\UpdateRoleRequest;
use App\Modules\Security\Http\Requests\Role\ListRoleRequest;
use App\Modules\Security\Http\Resources\Role\RoleResource;
use App\Modules\Security\Http\Resources\Role\RoleCollection;
use App\Modules\Security\DTO\RoleDTO;
use App\Modules\Security\Http\Requests\Role\RolePermissionsRequest;
use App\Modules\Security\Models\Role;

class RoleController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Role::class, 'role');
        $this->middleware('can:definePermissions,role')->only(['definePermissions']);
    }

    /**
     * @OA\Get(
     *      path="/api/security/roles",
     *      tags={"Role"},
     *      summary="List all rows",
     *      @OA\Parameter(name="id", in="query", required=false, @OA\Schema(type="integer")),
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
     *          description="Role list",
     *          @OA\JsonContent(ref="#/components/schemas/RoleCollection")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function index(ListRoleRequest $request, ListRoleAction $action): JsonResponse {
        $roleList = $action->execute($request->all());

        return response()->json(new RoleCollection($roleList), 200);
    }

    /**
     * @OA\Get(
     *      path="/api/security/roles/{id}",
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
     *          @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function show(int $id, ShowRoleAction $action): JsonResponse {
        $role = $action->execute($id);

        return response()->json(new RoleResource($role), 200);
    }

    /**
     * @OA\Post(
     *      path="/api/security/roles",
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
     *          @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function store(StoreRoleRequest $request, StoreRoleAction $action): JsonResponse {
        $dto = RoleDTO::fromArray($request->validated());
        $role = $action->execute($dto);

        return response()->json(new RoleResource($role), 201);
    }

    /**
     * @OA\Get(
     *      path="/api/security/roles/{id}/edit",
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
     *          @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      )
     * )
     */
    public function edit(int $id, EditRoleAction $action): JsonResponse {
        $role = $action->execute($id);

        return response()->json(new RoleResource($role), 200);
    }

    /**
     * @OA\Put(
     *      path="/api/security/roles/{id}",
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
     *          @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function update(int $id, UpdateRoleRequest $request, UpdateRoleAction $action): JsonResponse {
        $dto = RoleDTO::fromArray($request->validated());
        $role = $action->execute($id, $dto);

        return response()->json(new RoleResource($role), 200);
    }

    /**
     * @OA\Delete(
     *      path="/api/security/roles/{id}",
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
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(int $id, DeleteRoleAction $action): Response {
        $action->execute($id);
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
     *          @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function definePermissions(Role $role, RolePermissionsRequest $request, DefineRolePermissionsAction $action): JsonResponse {
        $role = $action->execute($role->id, $request->validated('ids'));
        return response()->json(new RoleResource($role), 200);
    }
}