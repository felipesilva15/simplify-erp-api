<?php

namespace App\Modules\Integration\Exceptions;

class IntegrationTimeoutException extends IntegrationException
{
    protected bool $retryable = true;

    public function __construct(string $message = 'Integração: timeout na requisição.', int $code = 0, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
