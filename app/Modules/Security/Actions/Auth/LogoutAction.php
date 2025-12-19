<?php

namespace App\Modules\Security\Actions\Auth;

use App\Modules\Security\Services\AuthService;

class LogoutAction
{
    public function __construct(protected AuthService $service) { }

    public function execute(): void {
        $this->service->logout();
    }
}