<?php

namespace App\Policies;

use App\Modules\Security\Models\Permission;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class PermissionPolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'permissions.viewAny');
    }

    public function view(User $user, Permission $model)
    {
        return $this->authService->hasAuthorized($user, 'permissions.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'permissions.create');
    }

    public function update(User $user, Permission $model)
    {
        return $this->authService->hasAuthorized($user, 'permissions.update');
    }

    public function delete(User $user, Permission $model)
    {
        return $this->authService->hasAuthorized($user, 'permissions.delete');
    }
}
