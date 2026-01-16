<?php

namespace App\Modules\Security\Actions\User;

use App\Core\DTO\ServiceResult;
use App\Modules\Security\DTO\UserDTO;
use App\Modules\Security\Services\UserService;
use App\Modules\Security\Models\User;

class StoreUserAction
{
    public function __construct(protected UserService $service) { }

    public function execute(UserDTO $data): ServiceResult {
        return $this->service->store($data);
    }
}