<?php

namespace App\Modules\Security\Actions\User;

use App\Core\DTO\ServiceResult;
use App\Modules\Security\DTO\UserDTO;
use App\Modules\Security\Services\UserService;
use App\Modules\Security\Models\User;

class EditUserAction
{
    public function __construct(protected UserService $service) { }

    public function execute(User $user): ServiceResult {
        return $this->service->edit($user);
    }
}