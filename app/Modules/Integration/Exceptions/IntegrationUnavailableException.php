<?php

namespace App\Modules\Integration\Exceptions;

class IntegrationUnavailableException extends IntegrationException
{
    protected bool $retryable = true;

    public function __construct(string $message = 'Integração indisponível.', int $code = 503, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
