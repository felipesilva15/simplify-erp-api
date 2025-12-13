<?php

namespace App\Modules\Auth\Actions\Permission;

use App\Modules\Auth\DTO\PermissionDTO;
use App\Modules\Auth\Services\PermissionService;
use App\Modules\Auth\Models\Permission;

class UpdatePermissionAction
{
    public function __construct(protected PermissionService $service) { }

    public function execute(int $id, PermissionDTO $data): Permission {
        return $this->service->update($id, $data);
    }
}