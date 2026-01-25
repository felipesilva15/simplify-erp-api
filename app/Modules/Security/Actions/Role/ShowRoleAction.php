<?php

namespace App\Modules\Security\Actions\Role;

use App\Core\DTO\ServiceResult;
use App\Modules\Security\Services\RoleService;
use App\Modules\Security\Models\Role;

class ShowRoleAction
{
    public function __construct(protected RoleService $service) { }

    public function execute(Role $role): ServiceResult {
        return $this->service->show($role);
    }
}