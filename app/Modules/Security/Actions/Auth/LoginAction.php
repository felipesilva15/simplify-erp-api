<?php

namespace App\Modules\Security\Actions\Auth;

use App\Modules\Security\DTO\AuthCredentialsDTO;
use App\Modules\Security\DTO\TokenDetailsDTO;
use App\Modules\Security\Services\AuthService;

class LoginAction
{
    public function __construct(protected AuthService $service) { }

    public function execute(AuthCredentialsDTO $credentials): TokenDetailsDTO {
        return $this->service->login($credentials);
    }
}