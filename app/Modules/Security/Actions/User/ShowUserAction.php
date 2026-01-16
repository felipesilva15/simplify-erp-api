<?php

namespace App\Modules\Security\Actions\User;

use App\Core\DTO\ServiceResult;
use App\Modules\Security\Services\UserService;
use App\Modules\Security\Models\User;

class ShowUserAction
{
    public function __construct(protected UserService $service) { }

    public function execute(User $user): ServiceResult {
        return $this->service->show($user);
    }
}