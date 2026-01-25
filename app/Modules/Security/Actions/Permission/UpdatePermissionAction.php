<?php

namespace App\Modules\Security\Actions\Permission;

use App\Core\DTO\ServiceResult;
use App\Modules\Security\DTO\PermissionDTO;
use App\Modules\Security\Services\PermissionService;
use App\Modules\Security\Models\Permission;

class UpdatePermissionAction
{
    public function __construct(protected PermissionService $service) { }

    public function execute(Permission $permission, PermissionDTO $data): ServiceResult {
        return $this->service->update($permission, $data);
    }
}