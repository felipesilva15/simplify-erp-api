<?php

namespace App\Modules\Auth\Actions\User;

use App\Modules\Auth\DTO\UserDTO;
use App\Modules\Auth\Services\UserService;
use App\Modules\Auth\Models\User;

class EditUserAction
{
    public function __construct(protected UserService $service) { }

    public function execute(int $id): User {
        return $this->service->edit($id);
    }
}