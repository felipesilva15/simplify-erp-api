<?php

namespace App\Policies;

use App\Modules\Finance\Models\PaymentMethod;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class PaymentMethodPolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'paymentMethods.viewAny');
    }

    public function view(User $user, PaymentMethod $model)
    {
        return $this->authService->hasAuthorized($user, 'paymentMethods.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'paymentMethods.create');
    }

    public function update(User $user, PaymentMethod $model)
    {
        return $this->authService->hasAuthorized($user, 'paymentMethods.update');
    }

    public function delete(User $user, PaymentMethod $model)
    {
        return $this->authService->hasAuthorized($user, 'paymentMethods.delete');
    }

    public function export(User $user)
    {
        return $this->authService->hasAuthorized($user, 'paymentMethods.export');
    }
}