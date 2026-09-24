<?php

namespace App\Policies;

use App\Core\Models\Country;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class CountryPolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'countries.viewAny');
    }

    public function view(User $user, Country $model)
    {
        return $this->authService->hasAuthorized($user, 'countries.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'countries.create');
    }

    public function update(User $user, Country $model)
    {
        return $this->authService->hasAuthorized($user, 'countries.update');
    }

    public function delete(User $user, Country $model)
    {
        return $this->authService->hasAuthorized($user, 'countries.delete');
    }
}