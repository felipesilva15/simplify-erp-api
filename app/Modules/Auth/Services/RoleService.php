<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Repositories\RoleRepositoryInterface;

class RoleService
{
    public function __construct(
        protected RoleRepositoryInterface $repo
    ) {}

    public function list(int $perPage = 15)
    {
        return $this->repo->paginate($perPage);
    }
}