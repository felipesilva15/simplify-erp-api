<?php

namespace App\Modules\Auth\Actions\User;

use App\Modules\Auth\Services\UserService;
use Illuminate\Pagination\LengthAwarePaginator;

class ListUserAction
{
    public function __construct(protected UserService $service) {}

    public function execute(array $filters): LengthAwarePaginator {
        return $this->service->list($filters);
    }
}