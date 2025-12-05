<?php

namespace App\Modules\Auth\Actions\Role;

use App\Modules\Auth\DTO\RoleDTO;
use App\Modules\Auth\Services\RoleService;
use App\Modules\Auth\Models\Role;

class StoreRoleAction
{
    public function __construct(protected RoleService $service) { }

    public function execute(RoleDTO $data): Role {
        return $this->service->store($data);
    }
}