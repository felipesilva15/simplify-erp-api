<?php

namespace App\Modules\Auth\DTO;

class RoleDTO
{
    public function __construct(
        public string $name,
        public string $status = 'active'
    ) {}
}