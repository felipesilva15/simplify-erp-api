<?php

namespace App\Policies;

use App\Modules\Catalog\Models\Product;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class ProductPolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'products.viewAny');
    }

    public function view(User $user, Product $model)
    {
        return $this->authService->hasAuthorized($user, 'products.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'products.create');
    }

    public function update(User $user, Product $model)
    {
        return $this->authService->hasAuthorized($user, 'products.update');
    }

    public function delete(User $user, Product $model)
    {
        return $this->authService->hasAuthorized($user, 'products.delete');
    }

    public function export(User $user)
    {
        return $this->authService->hasAuthorized($user, 'products.export');
    }
}