<?php

namespace App\Policies;

use App\Modules\Construction\Models\ProjectType;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class ProjectTypePolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'projectTypes.viewAny');
    }

    public function view(User $user, ProjectType $model)
    {
        return $this->authService->hasAuthorized($user, 'projectTypes.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'projectTypes.create');
    }

    public function update(User $user, ProjectType $model)
    {
        return $this->authService->hasAuthorized($user, 'projectTypes.update');
    }

    public function delete(User $user, ProjectType $model)
    {
        return $this->authService->hasAuthorized($user, 'projectTypes.delete');
    }

    public function export(User $user)
    {
        return $this->authService->hasAuthorized($user, 'projectTypes.export');
    }
}