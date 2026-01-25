<?php

namespace App\Modules\Security\Actions\Permission;

use App\Core\DTO\ServiceResult;
use App\Modules\Security\Services\PermissionService;
use App\Modules\Security\Models\Permission;

class ShowPermissionAction
{
    public function __construct(protected PermissionService $service) { }

    public function execute(Permission $permission): ServiceResult {
        return $this->service->show($permission);
    }
}