<?php

namespace App\Core\Exceptions;

use App\Core\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Symfony\Component\HttpFoundation\Response;

class ValidationException extends LaravelValidationException
{
    use ApiResponse;

    public function __construct($validator, $response = null, $errorBag = 'default') {
        parent::__construct($validator, $response, $errorBag);
    }

    public function render(Request $request): JsonResponse {
        return $this->error(
            message: 'Os dados enviados são inválidos.', 
            errors: $this->errors(),
            httpStatus: Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}