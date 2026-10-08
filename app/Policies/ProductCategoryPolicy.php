<?php

namespace App\Policies;

use App\Modules\Catalog\Models\ProductCategory;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class ProductCategoryPolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'productCategories.viewAny');
    }

    public function view(User $user, ProductCategory $model)
    {
        return $this->authService->hasAuthorized($user, 'productCategories.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'productCategories.create');
    }

    public function update(User $user, ProductCategory $model)
    {
        return $this->authService->hasAuthorized($user, 'productCategories.update');
    }

    public function delete(User $user, ProductCategory $model)
    {
        return $this->authService->hasAuthorized($user, 'productCategories.delete');
    }

    public function export(User $user)
    {
        return $this->authService->hasAuthorized($user, 'productCategories.export');
    }
}