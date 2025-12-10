<?php

namespace App\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use App\Core\Actions\Module\StoreModuleAction;
use App\Core\Actions\Module\UpdateModuleAction;
use App\Core\Actions\Module\DeleteModuleAction;
use App\Core\Actions\Module\EditModuleAction;
use App\Core\Actions\Module\ShowModuleAction;
use App\Core\Actions\Module\ListModuleAction;
use App\Core\Http\Requests\Module\StoreModuleRequest;
use App\Core\Http\Requests\Module\UpdateModuleRequest;
use App\Core\Http\Requests\Module\ListModuleRequest;
use App\Core\Http\Resources\Module\ModuleResource;
use App\Core\Http\Resources\Module\ModuleCollection;
use App\Core\DTO\ModuleDTO;

class ModuleController
{
    /**
     * @OA\Get(
     *      path="/api/core/modules",
     *      tags={"Module"},
     *      summary="List all rows",
     *      @OA\Parameter(name="id", in="query", required=false, @OA\Schema(type="integer")),
     *      @OA\Parameter(name="name", in="query", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="description", in="query", required=false, @OA\Schema(type="string")),
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
     *          description="Module list",
     *          @OA\JsonContent(ref="#/components/schemas/ModuleCollection")
     *      )
     * )
     */
    public function index(ListModuleRequest $request, ListModuleAction $action): JsonResponse {
        $moduleList = $action->execute($request->all());

        return response()->json(new ModuleCollection($moduleList), 200);
    }

    /**
     * @OA\Get(
     *      path="/api/core/modules/{id}",
     *      tags={"Module"},
     *      summary="List a module by ID",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Module ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Module data",
     *          @OA\JsonContent(ref="#/components/schemas/ModuleResource")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      )
     * )
     */
    public function show(int $id, ShowModuleAction $action): JsonResponse {
        $module = $action->execute($id);

        return response()->json(new ModuleResource($module), 200);
    }

    /**
     * @OA\Post(
     *      path="/api/core/modules",
     *      tags={"Module"},
     *      summary="Registers a module",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for creating a new module",
     *         @OA\JsonContent(ref="#/components/schemas/StoreModuleRequest")
     *      ),
     *      @OA\Response(
     *          response="201", 
     *          description="Registered module data",
     *          @OA\JsonContent(ref="#/components/schemas/ModuleResource")
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function store(StoreModuleRequest $request, StoreModuleAction $action): JsonResponse {
        $dto = ModuleDTO::fromArray($request->validated());
        $module = $action->execute($dto);

        return response()->json(new ModuleResource($module), 201);
    }

    /**
     * @OA\Get(
     *      path="/api/core/modules/{id}/edit",
     *      tags={"Module"},
     *      summary="Get data to edit a module",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Module ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Module data",
     *          @OA\JsonContent(ref="#/components/schemas/ModuleResource")
     *      ),
     *      @OA\Response(
     *          response="404", 
     *          description="Record not found",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorDTO")
     *      )
     * )
     */
    public function edit(int $id, EditModuleAction $action): JsonResponse {
        $module = $action->execute($id);

        return response()->json(new ModuleResource($module), 200);
    }

    /**
     * @OA\Put(
     *      path="/api/core/modules/{id}",
     *      tags={"Module"},
     *      summary="Update a module",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Module ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for update module",
     *         @OA\JsonContent(ref="#/components/schemas/UpdateModuleRequest")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Updated module data",
     *          @OA\JsonContent(ref="#/components/schemas/ModuleResource")
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
    public function update(int $id, UpdateModuleRequest $request, UpdateModuleAction $action): JsonResponse {
        $dto = ModuleDTO::fromArray($request->validated());
        $module = $action->execute($id, $dto);

        return response()->json(new ModuleResource($module), 200);
    }

    /**
     * @OA\Delete(
     *      path="/api/core/modules/{id}",
     *      tags={"Module"},
     *      summary="Delete a module",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Module ID",
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
    public function destroy(int $id, DeleteModuleAction $action): Response {
        $action->execute($id);
        return response()->noContent();
    }
}