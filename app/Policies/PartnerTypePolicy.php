<?php

namespace App\Policies;

use App\Modules\Partner\Models\PartnerType;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class PartnerTypePolicy
{
    public function __construct(protected AuthService $authService) { }

    public function viewAny(User $user)
    {
        return $this->authService->hasAuthorized($user, 'partnerTypes.viewAny');
    }

    public function view(User $user, PartnerType $model)
    {
        return $this->authService->hasAuthorized($user, 'partnerTypes.view');
    }

    public function create(User $user)
    {
        return $this->authService->hasAuthorized($user, 'partnerTypes.create');
    }

    public function update(User $user, PartnerType $model)
    {
        return $this->authService->hasAuthorized($user, 'partnerTypes.update');
    }

    public function delete(User $user, PartnerType $model)
    {
        return $this->authService->hasAuthorized($user, 'partnerTypes.delete');
    }
}
