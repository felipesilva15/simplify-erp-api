<?php

namespace App\Modules\Auth\Actions\User;

use App\Modules\Auth\Services\UserService;
use App\Modules\Auth\Models\User;

class ShowUserAction
{
    public function __construct(protected UserService $service) { }

    public function execute(int $id): User {
        return $this->service->findById($id);
    }
}