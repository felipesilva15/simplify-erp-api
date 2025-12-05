<?php

namespace App\Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use App\Modules\Auth\Actions\Role\StoreRoleAction;
use App\Modules\Auth\Actions\Role\UpdateRoleAction;
use App\Modules\Auth\Actions\Role\DeleteRoleAction;
use App\Modules\Auth\Actions\Role\EditRoleAction;
use App\Modules\Auth\Actions\Role\ShowRoleAction;
use App\Modules\Auth\Actions\Role\ListRoleAction;
use App\Modules\Auth\Http\Requests\Role\StoreRoleRequest;
use App\Modules\Auth\Http\Requests\Role\UpdateRoleRequest;
use App\Modules\Auth\Http\Requests\Role\ListRoleRequest;
use App\Modules\Auth\Http\Resources\RoleResource;
use App\Modules\Auth\Http\Resources\RoleCollection;
use App\Modules\Auth\DTO\RoleDTO;

class RoleController
{
    /**
     * @OA\Get(
     *      path="/api/auth/roles",
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
     *      )
     * )
     */
    public function index(ListRoleRequest $request, ListRoleAction $action): JsonResponse {
        $roleList = $action->execute($request->all());

        return response()->json(new RoleCollection($roleList), 200);
    }

    /**
     * @OA\Get(
     *      path="/api/auth/roles/{id}",
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
     *      )
     * )
     */
    public function show(int $id, ShowRoleAction $action): JsonResponse {
        $role = $action->execute($id);

        return response()->json(new RoleResource($role), 200);
    }

    /**
     * @OA\Post(
     *      path="/api/auth/roles",
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
     *      path="/api/auth/roles/{id}/edit",
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
     *      path="/api/auth/roles/{id}",
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
     *      path="/api/auth/roles/{id}",
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
}