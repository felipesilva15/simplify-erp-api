<?php

namespace App\Modules\Auth\Repositories;

use App\Modules\Auth\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    public function list(int $perPage = 15)
    {
        return Role::paginate($perPage);
    }
}