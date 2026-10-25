<?php

namespace App\Modules\Integration\Exceptions;

class IntegrationRejectedException extends IntegrationException
{
    protected bool $retryable = false;

    public function __construct(string $message = 'Integração: requisição rejeitada.', int $code = 422, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
