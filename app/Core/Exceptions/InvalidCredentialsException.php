<?php

namespace App\Core\Exceptions;

use App\Core\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidCredentialsException extends HttpException
{
    use ApiResponse;

    public function __construct(string $message = 'Credenciais inválidas.', int $code = 401, array $headers = []) {
        parent::__construct($code, $message, null, $headers, $code);
    }

    public function render(Request $request): JsonResponse {
        return $this->error(
            message: $this->message, 
            httpStatus: $this->code
        );
    }
}
