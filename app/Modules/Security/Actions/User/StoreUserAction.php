<?php

namespace App\Modules\Security\Actions\User;

use App\Modules\Security\DTO\UserDTO;
use App\Modules\Security\Services\UserService;
use App\Modules\Security\Models\User;

class StoreUserAction
{
    public function __construct(protected UserService $service) { }

    public function execute(UserDTO $data): User {
        return $this->service->store($data);
    }
}