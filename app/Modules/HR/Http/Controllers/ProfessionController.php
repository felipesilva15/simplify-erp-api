<?php

namespace App\Modules\HR\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Core\Http\Requests\Core\LookupRequest;
use App\Modules\HR\Http\Resources\Profession\ProfessionLookupCollection;

use App\Core\Http\Controllers\Controller;
use App\Core\Http\Requests\Core\ListRequest;
use App\Modules\HR\Http\Resources\Profession\ProfessionResource;
use App\Modules\HR\Http\Resources\Profession\ProfessionCollection;
use App\Modules\HR\Models\Profession;
use App\Modules\HR\Services\ProfessionService;
use App\Core\Traits\HasActivityLogs;
use App\Core\Traits\HasExcelExport;
use App\Modules\HR\Exports\ProfessionExport;
use InvalidArgumentException;

/**
 * @OA\PathItem(
 *     path="/api/hr/professions/{id}/activity-logs",
 *     @OA\Get(
 *         tags={"Profession"},
 *         summary="List activity logs of a profession",
 *         @OA\Parameter(
 *             name="id",
 *             in="path",
 *             required=true,
 *             description="Profession ID",
 *             @OA\Schema(type="integer")
 *         ),
 *         @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
 *         @OA\Response(
 *             response="200",
 *             description="Profession activity logs",
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
 *     path="/api/hr/professions/export",
 *     @OA\Get(
 *         tags={"Profession"},
 *         summary="Export all professions to Excel",
 *         operationId="exportProfession",
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
     @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
 *      *     @OA\Parameter(name="filters[id][lt]", in="query", required=false, @OA\Schema(type="integer")),
 *      *     @OA\Parameter(name="filters[id][lte]", in="query", required=false, @OA\Schema(type="integer")),
 *      *     @OA\Parameter(name="filters[id][gt]", in="query", required=false, @OA\Schema(type="integer")),
 *      *     @OA\Parameter(name="filters[id][gte]", in="query", required=false, @OA\Schema(type="integer")),
 *      *     @OA\Parameter(name="filters[id][ne]", in="query", required=false, @OA\Schema(type="integer")),
 *      *     @OA\Parameter(name="filters[cbo][eq]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[cbo][like]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[cbo][ne]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[name][eq]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[name][like]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[name][ne]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[created_at][eq]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[created_at][lt]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[created_at][lte]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[created_at][gt]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[created_at][gte]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[created_at][ne]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[updated_at][eq]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[updated_at][lt]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[updated_at][lte]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[updated_at][gt]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[updated_at][gte]", in="query", required=false, @OA\Schema(type="string")),
 *      *     @OA\Parameter(name="filters[updated_at][ne]", in="query", required=false, @OA\Schema(type="string")),
 *         @OA\Response(
 *             response="200",
 *             description="Excel file with professions",
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
class ProfessionController extends Controller
{
    use HasActivityLogs;
    use HasExcelExport;

    protected ProfessionService $service;

    public function __construct(ProfessionService $service) {
        $this->service = $service;
        $this->authorizeResource(Profession::class, 'profession');
    }

    protected function activityLogModelClass(): string
    {
        return Profession::class;
    }

    /**
     * @OA\Get(
     *      path="/api/hr/professions",
     *      tags={"Profession"},
     *      summary="List all professions",
     *     @OA\Parameter(name="filters[id][eq]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][lt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][lte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][gt]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][gte]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[id][ne]", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filters[cbo][eq]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[cbo][like]", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="filters[cbo][ne]", in="query", required=false, @OA\Schema(type="string")),
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
     *     @OA\Parameter(ref="#/components/parameters/sortsParam"),
     *     @OA\Parameter(ref="#/components/parameters/perPageParam"),
     *     @OA\Parameter(ref="#/components/parameters/pageParam"),
     *      @OA\Response(
     *          response="200", 
     *          description="Profession list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/ProfessionCollection")
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

        $paginated = new ProfessionCollection($serviceResult->data);
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
     *      path="/api/hr/professions/{profession}",
     *      tags={"Profession"},
     *      summary="List a profession by ID",
     *      @OA\Parameter(
     *         name="profession",
     *         in="path",
     *         required=true,
     *         description="Profession ID",
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response="200", 
     *          description="Profession data",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(
     *                      @OA\Property(
     *                          property="data",
     *                          ref="#/components/schemas/ProfessionResource"
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
    public function show(Profession $profession): JsonResponse {
        $serviceResult = $this->service->show($profession);

        return $this->success(
            data: new ProfessionResource($serviceResult->data),
            httpStatus: Response::HTTP_OK
        );
    }

    protected function exportModelClass(): string
    {
        return Profession::class;
    }

    protected function exportClassForFormat(string $format): string
    {
        return match ($format) {
            'full' => ProfessionExport::class,
            default => throw new InvalidArgumentException(
                "Formato de exportação [{$format}] não suportado."
            ),
        };
    }

    /**
     * @OA\Get(
     *      path="/api/hr/professions/lookup",
     *      tags={"Profession"},
     *      summary="Lookup professions",
     *      @OA\Parameter(name="q", in="query", description="Query for avaiable fields", required=false, @OA\Schema(type="string")),
     *      @OA\Parameter(name="keys[]", in="query", description="Keys to find", required=false, @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response="200", 
     *          description="profession lookup list",
     *          @OA\JsonContent(
     *              allOf={
     *                  @OA\Schema(ref="#/components/schemas/ApiResponse"),
     *                  @OA\Schema(ref="#/components/schemas/ProfessionLookupCollection")
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

        $paginated = new ProfessionLookupCollection($serviceResult->data);
        $paginated = $paginated->toArray($request);

        return $this->success(
            data: $paginated['data'],
            links: $paginated['links'],
            meta: $paginated['meta'],
            httpStatus: Response::HTTP_OK
        );
    }
}
