<?php

namespace App\Modules\Security\DTO;

class TokenDetailsDTO
{
    public function __construct(
        public string $token = '',
        public string $type = '',
        public int $expiresIn = 0
    ) { }
}