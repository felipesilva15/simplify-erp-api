<?php

namespace App\Policies;

use App\Modules\Partner\Models\Partner;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class PartnerPolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'partners.viewAny');
    }

    public function view(User $user, Partner $model)
    {
        return $this->authService->hasAuthorized($user, 'partners.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'partners.create');
    }

    public function update(User $user, Partner $model)
    {
        return $this->authService->hasAuthorized($user, 'partners.update');
    }

    public function delete(User $user, Partner $model)
    {
        return $this->authService->hasAuthorized($user, 'partners.delete');
    }
}
