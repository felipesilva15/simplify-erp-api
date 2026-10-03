<?php

namespace App\Core\Exceptions;

use App\Core\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NotFoundHttpException extends HttpException
{
    use ApiResponse;

    public function __construct(string $message = 'Registro não encontrado.', \Throwable $previous = null, int $code = Response::HTTP_NOT_FOUND, array $headers = []) {
        parent::__construct($code, $message, $previous, $headers, $code);
    }

    public function render(Request $request): JsonResponse {
        return $this->error(
            message: $this->message, 
            httpStatus: $this->code
        );
    }
}