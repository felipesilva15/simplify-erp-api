<?php

namespace App\Policies;

use App\Core\Models\City;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class CityPolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'cities.viewAny');
    }

    public function view(User $user, City $model)
    {
        return $this->authService->hasAuthorized($user, 'cities.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'cities.create');
    }

    public function update(User $user, City $model)
    {
        return $this->authService->hasAuthorized($user, 'cities.update');
    }

    public function delete(User $user, City $model)
    {
        return $this->authService->hasAuthorized($user, 'cities.delete');
    }
}