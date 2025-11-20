<?php

namespace App\Modules\Auth\Repositories;

interface RoleRepositoryInterface
{
    public function list(int $perPage = 15);
}