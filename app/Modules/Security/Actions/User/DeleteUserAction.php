<?php

namespace App\Modules\Security\Actions\User;

use App\Core\DTO\ServiceResult;
use App\Modules\Security\Models\User;
use App\Modules\Security\Services\UserService;

class DeleteUserAction
{
    public function __construct(protected UserService $service) {}

    public function execute(User $user): ServiceResult {
        return $this->service->delete($user);
    }
}