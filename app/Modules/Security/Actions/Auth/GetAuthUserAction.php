<?php

namespace App\Modules\Security\Actions\Auth;

use App\Modules\Security\Models\User;
use App\Modules\Security\Services\AuthService;

class GetAuthUserAction
{
    public function __construct(protected AuthService $service) { }

    public function execute(): User {
        return $this->service->getLoggedInUser();
    }
}