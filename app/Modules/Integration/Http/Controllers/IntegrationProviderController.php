<?php

namespace App\Modules\Integration\Http\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Modules\Integration\Registry\ProviderRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Integration",
 *     description="Integrations"
 * )
 */
class IntegrationProviderController extends Controller
{
    public function __construct(protected ProviderRegistry $registry)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/integrations/providers",
     *     tags={"Integration"},
     *     summary="List integration providers",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List", @OA\JsonContent(ref="#/components/schemas/ApiResponse"))
     * )
     */
    public function index(): JsonResponse
    {
        return $this->success(
            data: $this->registry->all()->toArray(),
            httpStatus: Response::HTTP_OK
        );
    }
}
