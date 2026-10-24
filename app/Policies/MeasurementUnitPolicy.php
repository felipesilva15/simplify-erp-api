<?php

namespace App\Policies;

use App\Modules\Catalog\Models\MeasurementUnit;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class MeasurementUnitPolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'measurementUnits.viewAny');
    }

    public function view(User $user, MeasurementUnit $model)
    {
        return $this->authService->hasAuthorized($user, 'measurementUnits.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'measurementUnits.create');
    }

    public function update(User $user, MeasurementUnit $model)
    {
        return $this->authService->hasAuthorized($user, 'measurementUnits.update');
    }

    public function delete(User $user, MeasurementUnit $model)
    {
        return $this->authService->hasAuthorized($user, 'measurementUnits.delete');
    }

    public function export(User $user)
    {
        return $this->authService->hasAuthorized($user, 'measurementUnits.export');
    }
}