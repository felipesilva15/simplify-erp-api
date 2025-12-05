<?php

namespace App\Modules\Auth\Actions\Role;

use App\Modules\Auth\Services\RoleService;
use Illuminate\Pagination\LengthAwarePaginator;

class ListRoleAction
{
    public function __construct(protected RoleService $service) {}

    public function execute(array $filters): LengthAwarePaginator {
        return $this->service->list($filters);
    }
}