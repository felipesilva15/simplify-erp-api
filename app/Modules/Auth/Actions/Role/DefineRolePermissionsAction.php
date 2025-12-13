<?php

namespace App\Modules\Auth\Actions\Role;

use App\Modules\Auth\DTO\RoleDTO;
use App\Modules\Auth\Services\RoleService;
use App\Modules\Auth\Models\Role;

class DefineRolePermissionsAction
{
    public function __construct(protected RoleService $service) { }

    public function execute(int $id, array $permissionIds): Role {
        return $this->service->definePermissions($id, $permissionIds);
    }
}