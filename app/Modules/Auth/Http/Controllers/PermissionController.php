<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Core\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use App\Modules\Auth\Actions\Permission\StorePermissionAction;
use App\Modules\Auth\Actions\Permission\UpdatePermissionAction;
use App\Modules\Auth\Actions\Permission\DeletePermissionAction;
use App\Modules\Auth\Actions\Permission\EditPermissionAction;
use App\Modules\Auth\Actions\Permission\ShowPermissionAction;
use App\Modules\Auth\Actions\Permission\ListPermissionAction;
use App\Modules\Auth\Http\Requests\Permission\StorePermissionRequest;
use App\Modules\Auth\Http\Requests\Permission\UpdatePermissionRequest;
use App\Modules\Auth\Http\Requests\Permission\ListPermissionRequest;
use App\Modules\Auth\Http\Resources\Permission\PermissionResource;
use App\Modules\Auth\Http\Resources\Permission\PermissionCollection;
use App\Modules\Auth\DTO\PermissionDTO;
use App\Modules\Auth\Models\Permission;

class PermissionController extends Controller
{
    public function __construct() {
        $this->authorizeResource(Permission::class, 'permission');
    }

    /**
     * @OA\Get(
     *      path="/api/auth/permissions",
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
     *          @OA\JsonContent(ref="#/components/schemas/PermissionCollection")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function index(ListPermissionRequest $request, ListPermissionAction $action): JsonResponse {
        $permissionList = $action->execute($request->all());

        return response()->json(new PermissionCollection($permissionList), 200);
    }

    /**
     * @OA\Get(
     *      path="/api/auth/permissions/{id}",
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
     *          @OA\JsonContent(ref="#/components/schemas/PermissionResource")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function show(int $id, ShowPermissionAction $action): JsonResponse {
        $permission = $action->execute($id);

        return response()->json(new PermissionResource($permission), 200);
    }

    /**
     * @OA\Post(
     *      path="/api/auth/permissions",
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
     *          @OA\JsonContent(ref="#/components/schemas/PermissionResource")
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function store(StorePermissionRequest $request, StorePermissionAction $action): JsonResponse {
        $dto = PermissionDTO::fromArray($request->validated());
        $permission = $action->execute($dto);

        return response()->json(new PermissionResource($permission), 201);
    }

    /**
     * @OA\Get(
     *      path="/api/auth/permissions/{id}/edit",
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
     *          @OA\JsonContent(ref="#/components/schemas/PermissionResource")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      )
     * )
     */
    public function edit(int $id, EditPermissionAction $action): JsonResponse {
        $permission = $action->execute($id);

        return response()->json(new PermissionResource($permission), 200);
    }

    /**
     * @OA\Put(
     *      path="/api/auth/permissions/{id}",
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
     *          @OA\JsonContent(ref="#/components/schemas/PermissionResource")
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
    public function update(Permission $permission, UpdatePermissionRequest $request, UpdatePermissionAction $action): JsonResponse {
        $dto = PermissionDTO::fromArray($request->validated());
        $permission = $action->execute($permission->id, $dto);

        return response()->json(new PermissionResource($permission), 200);
    }

    /**
     * @OA\Delete(
     *      path="/api/auth/permissions/{id}",
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
    public function destroy(int $id, DeletePermissionAction $action): Response {
        $action->execute($id);
        return response()->noContent();
    }
}