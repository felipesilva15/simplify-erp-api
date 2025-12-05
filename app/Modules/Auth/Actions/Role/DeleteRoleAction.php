<?php

namespace App\Modules\Auth\Actions\Role;

use App\Modules\Auth\Services\RoleService;

class DeleteRoleAction
{
    public function __construct(protected RoleService $service) {}

    public function execute(int $id): bool {
        return $this->service->delete($id);
    }
}