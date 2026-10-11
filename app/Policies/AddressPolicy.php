<?php

namespace App\Policies;

use App\Modules\Geography\Models\Address;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class AddressPolicy
{
    public function __construct(protected AuthService $authService) {}

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'addresses.viewAny');
    }

    public function view(User $user, Address $model)
    {
        return $this->authService->hasAuthorized($user, 'addresses.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'addresses.create');
    }

    public function update(User $user, Address $model)
    {
        return $this->authService->hasAuthorized($user, 'addresses.update');
    }

    public function delete(User $user, Address $model)
    {
        return $this->authService->hasAuthorized($user, 'addresses.delete');
    }
}
