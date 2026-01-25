<?php

namespace App\Modules\Security\Actions\Role;

use App\Core\DTO\ServiceResult;
use App\Modules\Security\DTO\RoleDTO;
use App\Modules\Security\Services\RoleService;
use App\Modules\Security\Models\Role;

class EditRoleAction
{
    public function __construct(protected RoleService $service) { }

    public function execute(Role $role): ServiceResult {
        return $this->service->edit($role);
    }
}