<?php

namespace App\Modules\Security\Actions\User;

use App\Modules\Security\Services\UserService;
use Illuminate\Pagination\LengthAwarePaginator;

class ListUserAction
{
    public function __construct(protected UserService $service) {}

    public function execute(array $filters): LengthAwarePaginator {
        return $this->service->list($filters);
    }
}