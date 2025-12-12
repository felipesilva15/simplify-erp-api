<?php

namespace App\Modules\Auth\Actions\User;

use App\Modules\Auth\DTO\UserDTO;
use App\Modules\Auth\Services\UserService;
use App\Modules\Auth\Models\User;

class UpdateUserAction
{
    public function __construct(protected UserService $service) { }

    public function execute(int $id, UserDTO $data): User {
        return $this->service->update($id, $data);
    }
}