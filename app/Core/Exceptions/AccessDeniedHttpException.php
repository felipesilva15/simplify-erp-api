<?php

namespace App\Core\Exceptions;

use App\Core\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AccessDeniedHttpException extends HttpException
{
    use ApiResponse;

    public function __construct(string $message = 'Você não possui permissão para acessar este recurso.', \Throwable $previous = null, int $code = 403, array $headers = []) {
        parent::__construct(403, $message, $previous, $headers, $code);
    }

    public function render(Request $request): JsonResponse {
        return $this->error(
            message: $this->message, 
            httpStatus: $this->code
        );
    }
}