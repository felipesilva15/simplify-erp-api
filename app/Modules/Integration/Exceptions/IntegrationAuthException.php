<?php

namespace App\Modules\Integration\Exceptions;

class IntegrationAuthException extends IntegrationException
{
    protected bool $retryable = false;

    public function __construct(string $message = 'Integração: falha de autenticação.', int $code = 401, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
