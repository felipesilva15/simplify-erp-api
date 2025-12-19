<?php

namespace App\Modules\Security\Actions\Role;

use App\Modules\Security\Services\RoleService;
use Illuminate\Pagination\LengthAwarePaginator;

class ListRoleAction
{
    public function __construct(protected RoleService $service) {}

    public function execute(array $filters): LengthAwarePaginator {
        return $this->service->list($filters);
    }
}