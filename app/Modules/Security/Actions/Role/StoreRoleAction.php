<?php

namespace App\Modules\Security\Actions\Role;

use App\Modules\Security\DTO\RoleDTO;
use App\Modules\Security\Services\RoleService;
use App\Modules\Security\Models\Role;

class StoreRoleAction
{
    public function __construct(protected RoleService $service) { }

    public function execute(RoleDTO $data): Role {
        return $this->service->store($data);
    }
}