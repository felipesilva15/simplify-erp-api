<?php

namespace App\Policies;

use App\Core\Models\State;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class StatePolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'states.viewAny');
    }

    public function view(User $user, State $model)
    {
        return $this->authService->hasAuthorized($user, 'states.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'states.create');
    }

    public function update(User $user, State $model)
    {
        return $this->authService->hasAuthorized($user, 'states.update');
    }

    public function delete(User $user, State $model)
    {
        return $this->authService->hasAuthorized($user, 'states.delete');
    }
}