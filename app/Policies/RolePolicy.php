<?php

namespace App\Policies;

use App\Core\Exceptions\NotFoundHttpException;
use App\Modules\Security\Models\Role;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;
use Illuminate\Support\Facades\Log;

class RolePolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'roles.viewAny');
    }

    public function view(User $user, Role $model)
    {
        return $this->authService->hasAuthorized($user, 'roles.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'roles.create');
    }

    public function update(User $user, Role $model)
    {
        return $this->authService->hasAuthorized($user, 'roles.update');
    }

    public function delete(User $user, Role $model)
    {
        return $this->authService->hasAuthorized($user, 'roles.delete');
    }

    public function restore(User $user, Role $model): bool
    {
        return $this->authService->hasAuthorized($user, 'roles.restore');
    }

    public function forceDelete(User $user, Role $model): bool
    {
        return $this->authService->hasAuthorized($user, 'roles.forceDelete');
    }

    public function definePermissions(User $user, Role $model): bool
    {
        return $this->authService->hasAuthorized($user, 'roles.definePermissions');
    }
}
