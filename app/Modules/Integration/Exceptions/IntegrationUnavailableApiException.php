<?php

namespace App\Modules\Integration\Exceptions;

use App\Core\Exceptions\BusinessRuleException;
use Illuminate\Http\Response;

class IntegrationUnavailableApiException extends BusinessRuleException
{
    public function __construct(string $message = 'Serviço de integração indisponível no momento.', array $errors = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, $errors, $previous, Response::HTTP_SERVICE_UNAVAILABLE, []);
    }
}
