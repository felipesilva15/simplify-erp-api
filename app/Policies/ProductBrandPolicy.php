<?php

namespace App\Policies;

use App\Modules\Catalog\Models\ProductBrand;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class ProductBrandPolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'productBrands.viewAny');
    }

    public function view(User $user, ProductBrand $model)
    {
        return $this->authService->hasAuthorized($user, 'productBrands.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'productBrands.create');
    }

    public function update(User $user, ProductBrand $model)
    {
        return $this->authService->hasAuthorized($user, 'productBrands.update');
    }

    public function delete(User $user, ProductBrand $model)
    {
        return $this->authService->hasAuthorized($user, 'productBrands.delete');
    }

    public function export(User $user)
    {
        return $this->authService->hasAuthorized($user, 'productBrands.export');
    }
}