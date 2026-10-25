<?php

namespace App\Modules\Integration\Exceptions;

use Exception;

class IntegrationException extends Exception
{
    protected bool $retryable = false;

    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function isRetryable(): bool
    {
        return $this->retryable;
    }

    public function setRetryable(bool $retryable): static
    {
        $this->retryable = $retryable;
        return $this;
    }
}
