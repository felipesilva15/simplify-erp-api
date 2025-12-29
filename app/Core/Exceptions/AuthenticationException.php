<?php

namespace App\Core\Exceptions;

use App\Core\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthenticationException extends HttpException
{
    use ApiResponse;

    public function __construct(string $message = 'Você não está autenticado para acessar este recurso.', \Throwable $previous = null, int $code = Response::HTTP_UNAUTHORIZED, array $headers = []) {
        parent::__construct(Response::HTTP_UNAUTHORIZED, $message, $previous, $headers, $code);
    }

    public function render(Request $request): JsonResponse {
        return $this->error(
            message: $this->message, 
            httpStatus: $this->code
        );
    }
}