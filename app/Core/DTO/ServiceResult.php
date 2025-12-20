<?php

namespace App\Core\DTO;

class ServiceResult
{
    public function __construct(
        public mixed $data,
        public array $warnings = [],
        public mixed $meta = []
    ) {}

    public function hasWarnings(): bool {
        return !empty($this->warnings);
    }
}