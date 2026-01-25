<?php

namespace App\Modules\Security\Actions\Role;

use App\Core\DTO\ServiceResult;
use App\Modules\Security\DTO\RoleDTO;
use App\Modules\Security\Services\RoleService;
use App\Modules\Security\Models\Role;

class UpdateRoleAction
{
    public function __construct(protected RoleService $service) { }

    public function execute(Role $role, RoleDTO $data): ServiceResult {
        return $this->service->update($role, $data);
    }
}