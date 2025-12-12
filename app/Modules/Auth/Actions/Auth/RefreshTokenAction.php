<?php

namespace App\Modules\Auth\Actions\Auth;

use App\Modules\Auth\DTO\TokenDetailsDTO;
use App\Modules\Auth\Services\AuthService;

class RefreshTokenAction
{
    public function __construct(protected AuthService $service) { }

    public function execute(): TokenDetailsDTO {
        return $this->service->refreshToken();
    }
}