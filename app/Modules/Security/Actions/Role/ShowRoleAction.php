<?php

namespace App\Modules\Security\Actions\Role;

use App\Modules\Security\Services\RoleService;
use App\Modules\Security\Models\Role;

class ShowRoleAction
{
    public function __construct(protected RoleService $service) { }

    public function execute(int $id): Role {
        return $this->service->findById($id);
    }
}