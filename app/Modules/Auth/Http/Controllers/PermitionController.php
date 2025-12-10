<?php

namespace App\Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use App\Modules\Auth\Actions\Permition\StorePermitionAction;
use App\Modules\Auth\Actions\Permition\UpdatePermitionAction;
use App\Modules\Auth\Actions\Permition\DeletePermitionAction;
use App\Modules\Auth\Actions\Permition\EditPermitionAction;
use App\Modules\Auth\Actions\Permition\ShowPermitionAction;
use App\Modules\Auth\Actions\Permition\ListPermitionAction;
use App\Modules\Auth\Http\Requests\Permition\StorePermitionRequest;
use App\Modules\Auth\Http\Requests\Permition\UpdatePermitionRequest;
use App\Modules\Auth\Http\Requests\Permition\ListPermitionRequest;
use App\Modules\Auth\Http\Resources\Permition\PermitionResource;
use App\Modules\Auth\Http\Resources\Permition\PermitionCollection;
use App\Modules\Auth\DTO\PermitionDTO;

class PermitionController
{
    /**
     * @OA\Get(
     *      path="/api/auth/permitions",
     *      tags={"Permition"},
     *      summary="List all rows",
     *      @OA\Parameter(name="id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="module_id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="group", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="action", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="description", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="has_access_free", in="query", required=false, @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="is_active", in="query", required=false, @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="created_at", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="updated_at", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="deleted_at", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="sort_by[]", in="query", description="Fields name's to sort", required=false, @OA\Schema(type="array", @OA\Items(type="string", example="id"))),
     *      @OA\Parameter(name="sort_dir[]", in="query", description="Directions to sort: Ascending = 'asc'; Descending = 'desc'", required=false, @OA\Schema(type="array", @OA\Items(type="string", example="desc"))),
     *      @OA\Parameter(name="per_page", in="query", description="Items per page", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response="200", 
     *          description="Permition list",
     *          @OA\JsonContent(ref="#/components/schemas/PermitionCollection")
     *      )
     * )
     */
    public function index(ListPermitionRequest $request, ListPermitionAction $action): JsonResponse {
        $permitionList = $action->execute($request->all());

        return response()->json(new PermitionCollection($permitionList), 200);
    }

    /**
     * @OA\Get(
     *      path="/api/auth/permitions/{id}",
     *      tags={"Permition"},
     *      summary="List a permition by ID",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Permition ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Permition data",
     *          @OA\JsonContent(ref="#/components/schemas/PermitionResource")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      )
     * )
     */
    public function show(int $id, ShowPermitionAction $action): JsonResponse {
        $permition = $action->execute($id);

        return response()->json(new PermitionResource($permition), 200);
    }

    /**
     * @OA\Post(
     *      path="/api/auth/permitions",
     *      tags={"Permition"},
     *      summary="Registers a permition",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for creating a new permition",
     *         @OA\JsonContent(ref="#/components/schemas/StorePermitionRequest")
     *      ),
     *      @OA\Response(
     *          response="201", 
     *          description="Registered permition data",
     *          @OA\JsonContent(ref="#/components/schemas/PermitionResource")
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function store(StorePermitionRequest $request, StorePermitionAction $action): JsonResponse {
        $dto = PermitionDTO::fromArray($request->validated());
        $permition = $action->execute($dto);

        return response()->json(new PermitionResource($permition), 201);
    }

    /**
     * @OA\Get(
     *      path="/api/auth/permitions/{id}/edit",
     *      tags={"Permition"},
     *      summary="Get data to edit a permition",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Permition ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Permition data",
     *          @OA\JsonContent(ref="#/components/schemas/PermitionResource")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      )
     * )
     */
    public function edit(int $id, EditPermitionAction $action): JsonResponse {
        $permition = $action->execute($id);

        return response()->json(new PermitionResource($permition), 200);
    }

    /**
     * @OA\Put(
     *      path="/api/auth/permitions/{id}",
     *      tags={"Permition"},
     *      summary="Update a permition",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Permition ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for update permition",
     *         @OA\JsonContent(ref="#/components/schemas/UpdatePermitionRequest")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Updated permition data",
     *          @OA\JsonContent(ref="#/components/schemas/PermitionResource")
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
    public function update(int $id, UpdatePermitionRequest $request, UpdatePermitionAction $action): JsonResponse {
        $dto = PermitionDTO::fromArray($request->validated());
        $permition = $action->execute($id, $dto);

        return response()->json(new PermitionResource($permition), 200);
    }

    /**
     * @OA\Delete(
     *      path="/api/auth/permitions/{id}",
     *      tags={"Permition"},
     *      summary="Delete a permition",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Permition ID",
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
    public function destroy(int $id, DeletePermitionAction $action): Response {
        $action->execute($id);
        return response()->noContent();
    }
}