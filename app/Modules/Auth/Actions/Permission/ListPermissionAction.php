<?php

namespace App\Modules\Auth\Actions\Permission;

use App\Modules\Auth\Services\PermissionService;
use Illuminate\Pagination\LengthAwarePaginator;

class ListPermissionAction
{
    public function __construct(protected PermissionService $service) {}

    public function execute(array $filters): LengthAwarePaginator {
        return $this->service->list($filters);
    }
}