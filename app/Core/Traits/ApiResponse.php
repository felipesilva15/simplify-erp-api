<?php

namespace App\Core\Traits;

use App\Core\DTO\ApiResponseDTO;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @OA\Schema(
 *   schema="ApiErrorResponse",
 *   type="object",
 *   required={"success","message"},
 *   allOf={
 *      @OA\Schema(ref="#/components/schemas/ApiResponse"),
 *      @OA\Schema(
 *          @OA\Property(property="errors", type="object", nullable=true)
 *      )
 *   }
 * )
 */
trait ApiResponse
{
    protected function success(
        string $message = 'Operação realizada com sucesso.',
        mixed $data = null,
        mixed $links = null,
        mixed $warnings = null,
        mixed $meta = null,
        int $httpStatus = Response::HTTP_OK
    ): JsonResponse {
        $response = new ApiResponseDTO(
            success: true,
            message: $message,
            data: $data,
            links: $links,
            warnings: $warnings,
            errors: null,
            meta: $meta
        );

        return response()->json($response->toArray(), $httpStatus);
    }

    protected function error(
        string $message,
        mixed $errors = null,
        int $httpStatus = Response::HTTP_BAD_REQUEST
    ): JsonResponse {
        $response = new ApiResponseDTO(
            success: false,
            message: $message,
            data: null,
            links: null,
            warnings: null,
            errors: $errors,
            meta: null
        );

        return response()->json($response->toArray(), $httpStatus);
    }
}