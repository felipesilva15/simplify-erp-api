<?php

namespace App\Policies;

use App\Core\Models\Resource;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class ResourcePolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'resources.viewAny');
    }

    public function view(User $user, Resource $model)
    {
        return $this->authService->hasAuthorized($user, 'resources.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'resources.create');
    }

    public function update(User $user, Resource $model)
    {
        return $this->authService->hasAuthorized($user, 'resources.update');
    }

    public function delete(User $user, Resource $model)
    {
        return $this->authService->hasAuthorized($user, 'resources.delete');
    }
}
