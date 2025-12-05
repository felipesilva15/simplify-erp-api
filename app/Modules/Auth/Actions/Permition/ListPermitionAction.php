<?php

namespace App\Modules\Auth\Actions\Permition;

use App\Modules\Auth\Services\PermitionService;
use Illuminate\Pagination\LengthAwarePaginator;

class ListPermitionAction
{
    public function __construct(protected PermitionService $service) {}

    public function execute(array $filters): LengthAwarePaginator {
        return $this->service->list($filters);
    }
}