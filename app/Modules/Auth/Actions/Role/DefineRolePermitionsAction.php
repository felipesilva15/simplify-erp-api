<?php

namespace App\Modules\Auth\Actions\Role;

use App\Modules\Auth\DTO\RoleDTO;
use App\Modules\Auth\Services\RoleService;
use App\Modules\Auth\Models\Role;

class DefineRolePermitionsAction
{
    public function __construct(protected RoleService $service) { }

    public function execute(int $id, array $permitionIds): Role {
        return $this->service->definePermitions($id, $permitionIds);
    }
}