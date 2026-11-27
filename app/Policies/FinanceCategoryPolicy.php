<?php

namespace App\Policies;

use App\Modules\Finance\Models\FinanceCategory;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class FinanceCategoryPolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'financeCategories.viewAny');
    }

    public function view(User $user, FinanceCategory $model)
    {
        return $this->authService->hasAuthorized($user, 'financeCategories.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'financeCategories.create');
    }

    public function update(User $user, FinanceCategory $model)
    {
        return $this->authService->hasAuthorized($user, 'financeCategories.update');
    }

    public function delete(User $user, FinanceCategory $model)
    {
        return $this->authService->hasAuthorized($user, 'financeCategories.delete');
    }

    public function export(User $user)
    {
        return $this->authService->hasAuthorized($user, 'financeCategories.export');
    }
}