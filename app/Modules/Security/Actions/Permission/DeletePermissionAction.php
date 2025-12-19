<?php

namespace App\Modules\Security\Actions\Permission;

use App\Modules\Security\Services\PermissionService;

class DeletePermissionAction
{
    public function __construct(protected PermissionService $service) {}

    public function execute(int $id): bool {
        return $this->service->delete($id);
    }
}