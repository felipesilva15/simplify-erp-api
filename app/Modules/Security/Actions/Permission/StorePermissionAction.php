<?php

namespace App\Modules\Security\Actions\Permission;

use App\Modules\Security\DTO\PermissionDTO;
use App\Modules\Security\Services\PermissionService;
use App\Modules\Security\Models\Permission;

class StorePermissionAction
{
    public function __construct(protected PermissionService $service) { }

    public function execute(PermissionDTO $data): Permission {
        return $this->service->store($data);
    }
}