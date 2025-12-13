<?php

namespace App\Modules\Auth\Actions\Permission;

use App\Modules\Auth\Services\PermissionService;
use App\Modules\Auth\Models\Permission;

class ShowPermissionAction
{
    public function __construct(protected PermissionService $service) { }

    public function execute(int $id): Permission {
        return $this->service->findById($id);
    }
}