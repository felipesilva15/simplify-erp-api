<?php

namespace App\Core\Exceptions;

use App\Core\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AccessDeniedHttpException extends HttpException
{
    use ApiResponse;

    public function __construct(string $message = 'Você não possui permissão para acessar este recurso.', \Throwable $previous = null, int $code = Response::HTTP_FORBIDDEN, array $headers = []) {
        parent::__construct(Response::HTTP_FORBIDDEN, $message, $previous, $headers, $code);
    }

    public function render(Request $request): JsonResponse {
        return $this->error(
            message: $this->message, 
            httpStatus: $this->code
        );
    }
}