<?php

namespace App\Modules\Security\Actions\Permission;

use App\Core\DTO\ServiceResult;
use App\Modules\Security\Models\Permission;
use App\Modules\Security\Services\PermissionService;

class DeletePermissionAction
{
    public function __construct(protected PermissionService $service) {}

    public function execute(Permission $permission): ServiceResult {
        return $this->service->delete($permission);
    }
}