<?php

namespace App\Modules\Auth\Actions\Role;

use App\Modules\Auth\Services\RoleService;
use App\Modules\Auth\Models\Role;

class ShowRoleAction
{
    public function __construct(protected RoleService $service) { }

    public function execute(int $id): Role {
        return $this->service->findById($id);
    }
}