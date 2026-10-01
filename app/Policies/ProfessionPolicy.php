<?php

namespace App\Policies;

use App\Modules\HR\Models\Profession;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class ProfessionPolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'professions.viewAny');
    }

    public function view(User $user, Profession $model)
    {
        return $this->authService->hasAuthorized($user, 'professions.view');
    }

    public function export(User $user)
    {
        return $this->authService->hasAuthorized($user, 'professions.export');
    }
}