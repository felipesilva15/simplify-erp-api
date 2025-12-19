<?php

namespace App\Modules\Security\Actions\Permission;

use App\Modules\Security\Services\PermissionService;
use App\Modules\Security\Models\Permission;

class ShowPermissionAction
{
    public function __construct(protected PermissionService $service) { }

    public function execute(int $id): Permission {
        return $this->service->findById($id);
    }
}