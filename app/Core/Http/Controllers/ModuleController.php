<?php

namespace App\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Core\Http\Requests\Module\StoreModuleRequest;
use App\Core\Http\Requests\Module\UpdateModuleRequest;
use App\Core\Http\Resources\Module\ModuleResource;
use App\Core\Http\Resources\Module\ModuleCollection;
use App\Core\DTO\ModuleDTO;
use App\Core\Http\Requests\Core\ListRequest;
use App\Core\Models\Module;
use App\Core\Services\ModuleService;

class ModuleController extends Controller
{
    protected ModuleService $service;

    public function __construct(ModuleService $service) {
        $this->service = $service;
        $this->authorizeResource(Module::class, 'module');
    }

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
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/ModuleCollection")
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

        $paginated = new ModuleCollection($serviceResult->data);
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
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ModuleResource"
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
    public function show(Module $module): JsonResponse {
        $serviceResult = $this->service->show($module);

        return $this->success(
            data: new ModuleResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
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
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ModuleResource"
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
    public function store(StoreModuleRequest $request): JsonResponse {
        $dto = ModuleDTO::fromArray($request->validated());
        $serviceResult = $this->service->store($dto);

        return $this->success(
            data: new ModuleResource($serviceResult->data),
            httpStatus: Response::HTTP_CREATED
        );
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
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ModuleResource"
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
    public function edit(Module $module): JsonResponse {
        $serviceResult = $this->service->edit($module);

        return $this->success(
            data: new ModuleResource($serviceResult->data),
            warnings: $serviceResult->warnings,
            meta: $serviceResult->meta,
            httpStatus: Response::HTTP_OK
        );
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
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ModuleResource"
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
    public function update(Module $module, UpdateModuleRequest $request): JsonResponse {
        $dto = ModuleDTO::fromArray($request->validated());
        $serviceResult = $this->service->update($module, $dto);

        return $this->success(
            data: new ModuleResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
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
    public function destroy(Module $module): Response {
        $this->service->delete($module);
        return response()->noContent();
    }
}