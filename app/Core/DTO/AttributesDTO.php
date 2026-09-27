<?php

namespace App\Core\DTO;

class AttributesDTO
{
    public function __construct(
        public array $attributes = []
    ) { }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
