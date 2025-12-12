<?php

namespace App\Modules\Auth\Actions\User;

use App\Modules\Auth\Services\UserService;

class DeleteUserAction
{
    public function __construct(protected UserService $service) {}

    public function execute(int $id): bool {
        return $this->service->delete($id);
    }
}