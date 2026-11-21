<?php

namespace App\Modules\Construction\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Core\Http\Requests\Core\LookupRequest;
use App\Modules\Construction\Http\Resources\ProjectType\ProjectTypeLookupCollection;

use App\Core\Http\Controllers\Controller;
use App\Core\Http\Requests\Core\ListRequest;
use App\Modules\Construction\Http\Requests\ProjectType\StoreProjectTypeRequest;
use App\Modules\Construction\Http\Requests\ProjectType\UpdateProjectTypeRequest;
use App\Modules\Construction\Http\Resources\ProjectType\ProjectTypeResource;
use App\Modules\Construction\Http\Resources\ProjectType\ProjectTypeCollection;
use App\Modules\Construction\DTO\ProjectTypeDTO;
use App\Modules\Construction\Models\ProjectType;
use App\Modules\Construction\Services\ProjectTypeService;
use App\Core\Traits\HasActivityLogs;
use App\Core\Traits\HasExcelExport;
use App\Modules\Construction\Exports\ProjectTypeExport;
use InvalidArgumentException;

/**
 * @OA\PathItem(
 *     path="/api/construction/project-types/{id}/activity-logs",
 *     @OA\Get(
 *         tags={"ProjectType"},
 *         summary="List activity logs of a project type",
 *         operationId="listProjectTypeActivityLogs",
 *         @OA\Parameter(
 *             name="id",
 *             in="path",
 *             required=true,
 *             description="ProjectType ID",
 *             @OA\Schema(type="integer")
 *         ),
 *         @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Response(
 *             response="200",
 *             description="ProjectType activity logs",
 *             @OA\JsonContent(
 *                 allOf={
 *                     @OA\Schema(ref="#/components/schemas/ApiResponse"),
 *                     @OA\Schema(ref="#/components/schemas/ActivityLogCollection")
 *                 }
 *             )
 *         ),
 *         @OA\Response(
 *             response="401",
 *             description="Unauthorized",
 *             @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
 *         ),
 *         @OA\Response(
 *             response="403",
 *             description="Forbidden",
 *             @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
 *         ),
 *         @OA\Response(
 *             response="404",
 *             description="Record not found",
 *             @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
 *         ),
 *         security={{"bearerAuth":{}}}
 *     )
 * )
 * @OA\PathItem(
 *     path="/api/construction/project-types/export",
 *     @OA\Get(
 *         tags={"ProjectType"},
 *         summary="Export all project types to Excel",
 *         operationId="exportProjectType",
 *         @OA\Parameter(ref="#/components/parameters/qParam"),
 *         @OA\Parameter(ref="#/components/parameters/sortsParam"),
 *         @OA\Parameter(ref="#/components/parameters/perPageParam"),
 *         @OA\Parameter(ref="#/components/parameters/pageParam"),
 *         @OA\Parameter(
 *             name="format",
 *             in="query",
 *             required=false,
 *             description="Export format (full, summarized, detailed)",
 *             @OA\Schema(type="string", enum={"full","summarized","detailed"})
 *         ),
 *         @OA\Parameter(
 *             name="extension",
 *             in="query",
 *             required=false,
 *             description="File extension (xlsx, xls, csv)",
 *             @OA\Schema(type="string", enum={"xlsx","xls","csv"})
 *         ),
 *     @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[name][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][ne]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Response(
 *             response="200",
 *             description="Excel file with project types",
 *             @OA\MediaType(
 *                 mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
 *             )
 *         ),
 *         @OA\Response(
 *             response="401",
 *             description="Unauthorized",
 *             @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
 *         ),
 *         @OA\Response(
 *             response="403",
 *             description="Forbidden",
 *             @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
 *         ),
 *         security={{"bearerAuth":{}}}
 *     )
 * )
 */
class ProjectTypeController extends Controller
{
    use HasActivityLogs;
    use HasExcelExport;

    protected ProjectTypeService $service;

    public function __construct(ProjectTypeService $service) {
        $this->service = $service;
        $this->authorizeResource(ProjectType::class, 'project_type');
    }

    protected function activityLogModelClass(): string
    {
        return ProjectType::class;
    }

    /**
     * @OA\Get(
     *      path="/api/construction/project-types",
     *      tags={"ProjectType"},
     *      summary="List all project types",
     *     @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[name][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[name][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[created_at][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[updated_at][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][lt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][lte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][gt]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][gte]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[deleted_at][ne]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(ref="#/components/parameters/qParam"),
     *     @OA\Parameter(ref="#/components/parameters/sortsParam"),
     *     @OA\Parameter(ref="#/components/parameters/perPageParam"),
     *     @OA\Parameter(ref="#/components/parameters/pageParam"),
     *      @OA\Response(
     *          response="200", 
     *          description="ProjectType list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/ProjectTypeCollection")
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

        $paginated = new ProjectTypeCollection($serviceResult->data);
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
     *      path="/api/construction/project-types/{project_type}",
     *      tags={"ProjectType"},
     *      summary="List a project type by ID",
     *      @OA\Parameter(
     *         name="project_type",
     *         in="path",
     *         required=true,
     *         description="ProjectType ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="ProjectType data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ProjectTypeResource"
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
    public function show(ProjectType $project_type): JsonResponse {
        $serviceResult = $this->service->show($project_type);

        return $this->success(
            data: new ProjectTypeResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     *      path="/api/construction/project-types",
     *      tags={"ProjectType"},
     *      summary="Registers a project type",
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for creating a new project type",
     *         @OA\JsonContent(ref="#/components/schemas/StoreProjectTypeRequest")
     *      ),
     *      @OA\Response(
     *          response="201", 
     *          description="Registered project type data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ProjectTypeResource"
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
     *          response="422", 
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function store(StoreProjectTypeRequest $request): JsonResponse {
        $dto = ProjectTypeDTO::fromArray($request->validated());
        $serviceResult = $this->service->store($dto);

        return $this->success(
            data: new ProjectTypeResource($serviceResult->data),
            httpStatus: Response::HTTP_CREATED
        );
    }

    /**
     * @OA\Get(
     *      path="/api/construction/project-types/{project_type}/edit",
     *      tags={"ProjectType"},
     *      summary="Get data to edit a project type",
     *      @OA\Parameter(
     *         name="project_type",
     *         in="path",
     *         required=true,
     *         description="ProjectType ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="ProjectType data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ProjectTypeResource"
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
    public function edit(ProjectType $project_type): JsonResponse {
        $serviceResult = $this->service->edit($project_type);

        return $this->success(
            data: new ProjectTypeResource($serviceResult->data),
            warnings: $serviceResult->warnings,
            meta: $serviceResult->meta,
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Put(
     *      path="/api/construction/project-types/{project_type}",
     *      tags={"ProjectType"},
     *      summary="Update a project type",
     *      @OA\Parameter(
     *         name="project_type",
     *         in="path",
     *         required=true,
     *         description="ProjectType ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         description="Data for update project type",
     *         @OA\JsonContent(ref="#/components/schemas/UpdateProjectTypeRequest")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Updated project type data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ProjectTypeResource"
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
     *      @OA\Response(
     *          response="422", 
     *          description="Unprocessable Entity",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function update(ProjectType $project_type, UpdateProjectTypeRequest $request): JsonResponse {
        $dto = ProjectTypeDTO::fromArray($request->validated());
        $serviceResult = $this->service->update($project_type, $dto);

        return $this->success(
            data: new ProjectTypeResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    /**
     * @OA\Delete(
     *      path="/api/construction/project-types/{project_type}",
     *      tags={"ProjectType"},
     *      summary="Delete a project type",
     *      @OA\Parameter(
     *         name="project_type",
     *         in="path",
     *         required=true,
     *         description="ProjectType ID",
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
    public function destroy(ProjectType $project_type): Response {
        $this->service->delete($project_type);
        return response()->noContent();
    }

    protected function exportModelClass(): string
    {
        return ProjectType::class;
    }

    protected function exportClassForFormat(string $format): string
    {
        return match ($format) {
            'full' => ProjectTypeExport::class,
            default => throw new InvalidArgumentException(
                "Formato de exportação [{$format}] não suportado."
            ),
        };
    }/**
     * @OA\Get(
     *      path="/api/construction/project-types/lookup",
     *      tags={"ProjectType"},
     *      summary="Lookup project types",
     *      @OA\Parameter(ref="#/components/parameters/qParam"),
     *      @OA\Parameter(name="keys[]", in="query", description="Keys to find", required=false, @OA\Schema(type="{{lookup_key_type}}")),
     *      @OA\Parameter(ref="#/components/parameters/sortsParam"),
     *      @OA\Parameter(ref="#/components/parameters/perPageParam"),
     *      @OA\Parameter(ref="#/components/parameters/pageParam"),
     *      @OA\Response(
     *          response="200", 
     *          description="project type lookup list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/ProjectTypeLookupCollection")
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response="401", 
     *          description="Unauthorized",
     *          @OA\JsonContent(ref="#/components/schemas/ApiErrorResponse")
     *      ),
     *      security={{"bearerAuth":{}}}
     * )
     */
    public function lookup(LookupRequest $request): JsonResponse {
        $serviceResult = $this->service->lookup($request->all());

        $paginated = new ProjectTypeLookupCollection($serviceResult->data);
        $paginated = $paginated->toArray($request);

        return $this->success(
            data: $paginated['data'],
            links: $paginated['links'],
            meta: $paginated['meta'],
            httpStatus: Response::HTTP_OK
        );
    }
}