<?php

namespace App\Core\DTO;

class LookupItem
{
    public function __construct(
        public mixed $key,
        public string $label,
        public string $sublabel,
        public mixed $meta = []
    ) {}
}