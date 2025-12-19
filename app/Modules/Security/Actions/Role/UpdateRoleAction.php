<?php

namespace App\Modules\Security\Actions\Role;

use App\Modules\Security\DTO\RoleDTO;
use App\Modules\Security\Services\RoleService;
use App\Modules\Security\Models\Role;

class UpdateRoleAction
{
    public function __construct(protected RoleService $service) { }

    public function execute(int $id, RoleDTO $data): Role {
        return $this->service->update($id, $data);
    }
}