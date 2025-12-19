<?php

namespace App\Modules\Security\Actions\Role;

use App\Modules\Security\Services\RoleService;

class DeleteRoleAction
{
    public function __construct(protected RoleService $service) {}

    public function execute(int $id): bool {
        return $this->service->delete($id);
    }
}