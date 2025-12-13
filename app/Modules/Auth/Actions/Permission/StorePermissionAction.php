<?php

namespace App\Modules\Auth\Actions\Permission;

use App\Modules\Auth\DTO\PermissionDTO;
use App\Modules\Auth\Services\PermissionService;
use App\Modules\Auth\Models\Permission;

class StorePermissionAction
{
    public function __construct(protected PermissionService $service) { }

    public function execute(PermissionDTO $data): Permission {
        return $this->service->store($data);
    }
}