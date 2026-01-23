<?php

namespace App\Policies;

use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class UserPolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'users.viewAny');
    }

    public function view(User $user, User $model)
    {
        return $this->authService->hasAuthorized($user, 'users.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'users.create');
    }

    public function update(User $user, User $model)
    {
        return $this->authService->hasAuthorized($user, 'users.update');
    }

    public function delete(User $user, User $model)
    {
        return $this->authService->hasAuthorized($user, 'users.delete');
    }
}
