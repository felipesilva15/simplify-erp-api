<?php

namespace App\Modules\Security\Actions\User;

use App\Modules\Security\Services\UserService;

class DeleteUserAction
{
    public function __construct(protected UserService $service) {}

    public function execute(int $id): bool {
        return $this->service->delete($id);
    }
}