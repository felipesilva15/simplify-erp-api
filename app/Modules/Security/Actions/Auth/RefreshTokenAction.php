<?php

namespace App\Modules\Security\Actions\Auth;

use App\Modules\Security\DTO\TokenDetailsDTO;
use App\Modules\Security\Services\AuthService;

class RefreshTokenAction
{
    public function __construct(protected AuthService $service) { }

    public function execute(): TokenDetailsDTO {
        return $this->service->refreshToken();
    }
}