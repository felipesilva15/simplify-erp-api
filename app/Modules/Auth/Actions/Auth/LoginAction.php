<?php

namespace App\Modules\Auth\Actions\Auth;

use App\Modules\Auth\DTO\AuthCredentialsDTO;
use App\Modules\Auth\DTO\TokenDetailsDTO;
use App\Modules\Auth\Services\AuthService;

class LoginAction
{
    public function __construct(protected AuthService $service) { }

    public function execute(AuthCredentialsDTO $credentials): TokenDetailsDTO {
        return $this->service->login($credentials);
    }
}