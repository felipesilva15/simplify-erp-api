<?php

namespace App\Core\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @OA\Schema(
 *      schema="ApiErrorDTO",
 *      @OA\Property(property="path", type="string", example="/api/route"),
 *      @OA\Property(property="code", type="integer", example=500),
 *      @OA\Property(property="message", type="string", example="Error ocurried")
 * )
 */
class NotFoundHttpException extends HttpException
{
    public function __construct(string $message = 'Registro não encontrado.', \Throwable $previous = null, int $code = 404, array $headers = []) {
        parent::__construct(404, $message, $previous, $headers, $code);
    }

    public function render(Request $request): JsonResponse {
        return response()->json([
            'path' => $request->path(),
            'code' => $this->code,
            'message' => $this->message
        ], $this->code);
    }
}