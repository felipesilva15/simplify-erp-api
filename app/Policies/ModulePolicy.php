<?php

namespace App\Policies;

use App\Core\Models\Module;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class ModulePolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'modules.viewAny');
    }

    public function view(User $user, Module $model)
    {
        return $this->authService->hasAuthorized($user, 'modules.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'modules.create');
    }

    public function update(User $user, Module $model)
    {
        return $this->authService->hasAuthorized($user, 'modules.update');
    }

    public function delete(User $user, Module $model)
    {
        return $this->authService->hasAuthorized($user, 'modules.delete');
    }

    public function restore(User $user, Module $model): bool
    {
        return $this->authService->hasAuthorized($user, 'modules.restore');
    }

    public function forceDelete(User $user, Module $model): bool
    {
        return $this->authService->hasAuthorized($user, 'modules.forceDelete');
    }
}
