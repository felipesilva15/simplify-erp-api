<?php

namespace App\Modules\Security\Actions\Role;

use App\Core\DTO\ServiceResult;
use App\Modules\Security\Services\RoleService;

class LookupRoleAction
{
    public function __construct(protected RoleService $service) {}

    public function execute(array $params): ServiceResult {
        return $this->service->lookup($params);
    }
}