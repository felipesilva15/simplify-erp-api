<?php

namespace App\Core\Exceptions;

use App\Core\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @OA\Schema(
 *   schema="ApiBusinessRuleErrorResponse",
 *   type="object",
 *   required={"success","message"},
 *   allOf={
 *      @OA\Schema(ref="#/components/schemas/ApiErrorResponse"),
 *      @OA\Schema(
 *          @OA\Property(property="errors", type="object", nullable=true, example={"contacts.0.email": "Informe ao menos um meio de contato."})
 *      )
 *   }
 * )
 */
class BusinessRuleException extends HttpException
{
    use ApiResponse;

    public function __construct(
        string $message = 'Uma ou mais regras de negócio não foram atendidas.',
        private readonly array $errors = [],
        \Throwable $previous = null,
        int $code = Response::HTTP_UNPROCESSABLE_ENTITY,
        array $headers = []
    ) {
        parent::__construct($code, $message, $previous, $headers, $code);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function render(Request $request): JsonResponse {
        return $this->error(
            message: $this->message,
            errors: $this->errors ?: null,
            httpStatus: $this->code
        );
    }
}
