<?php

namespace App\Policies;

use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class WarehousePolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'warehouses.viewAny');
    }

    public function view(User $user, Warehouse $model)
    {
        return $this->authService->hasAuthorized($user, 'warehouses.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'warehouses.create');
    }

    public function update(User $user, Warehouse $model)
    {
        return $this->authService->hasAuthorized($user, 'warehouses.update');
    }

    public function delete(User $user, Warehouse $model)
    {
        return $this->authService->hasAuthorized($user, 'warehouses.delete');
    }

    public function export(User $user)
    {
        return $this->authService->hasAuthorized($user, 'warehouses.export');
    }
}