<?php

namespace App\Modules\Auth\Actions\Auth;

use App\Modules\Auth\Services\AuthService;

class LogoutAction
{
    public function __construct(protected AuthService $service) { }

    public function execute(): void {
        $this->service->logout();
    }
}