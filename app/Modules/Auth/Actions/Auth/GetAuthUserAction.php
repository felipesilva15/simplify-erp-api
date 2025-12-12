<?php

namespace App\Modules\Auth\Actions\Auth;

use App\Modules\Auth\Models\User;
use App\Modules\Auth\Services\AuthService;

class GetAuthUserAction
{
    public function __construct(protected AuthService $service) { }

    public function execute(): User {
        return $this->service->getLoggedInUser();
    }
}