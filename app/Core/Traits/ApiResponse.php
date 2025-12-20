<?php

namespace App\Core\Traits;

use App\Core\DTO\ApiResponseDTO;
use Symfony\Component\HttpFoundation\JsonResponse;

trait ApiResponse
{
    protected function success(
        string $message = 'Operação realizada com sucesso.',
        mixed $data = null,
        mixed $links = null,
        mixed $warnings = null,
        mixed $meta = null,
        int $httpStatus = 200
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
        int $httpStatus = 400
    ) {
        $response = [
            'success' => false,
            'message' => $message,
            'data'    => null,
            'errors'  => $errors,
            'meta'    => null
        ];

        return response()->json($response, $httpStatus);
    }
}