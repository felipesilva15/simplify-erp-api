<?php

namespace App\Modules\Security\Actions\Permission;

use App\Modules\Security\Services\PermissionService;
use Illuminate\Pagination\LengthAwarePaginator;

class ListPermissionAction
{
    public function __construct(protected PermissionService $service) {}

    public function execute(array $filters): LengthAwarePaginator {
        return $this->service->list($filters);
    }
}