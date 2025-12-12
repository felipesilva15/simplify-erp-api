<?php

namespace App\Modules\Auth\Actions\User;

use App\Modules\Auth\DTO\UserDTO;
use App\Modules\Auth\Services\UserService;
use App\Modules\Auth\Models\User;

class StoreUserAction
{
    public function __construct(protected UserService $service) { }

    public function execute(UserDTO $data): User {
        return $this->service->store($data);
    }
}