<?php

namespace App\Modules\Auth\Actions\Permission;

use App\Modules\Auth\Services\PermissionService;

class DeletePermissionAction
{
    public function __construct(protected PermissionService $service) {}

    public function execute(int $id): bool {
        return $this->service->delete($id);
    }
}