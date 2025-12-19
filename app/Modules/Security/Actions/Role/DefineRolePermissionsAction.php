<?php

namespace App\Modules\Security\Actions\Role;

use App\Modules\Security\DTO\RoleDTO;
use App\Modules\Security\Services\RoleService;
use App\Modules\Security\Models\Role;

class DefineRolePermissionsAction
{
    public function __construct(protected RoleService $service) { }

    public function execute(int $id, array $permissionIds): Role {
        return $this->service->definePermissions($id, $permissionIds);
    }
}