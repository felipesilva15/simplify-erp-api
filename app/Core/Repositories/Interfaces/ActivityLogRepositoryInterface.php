<?php

namespace App\Core\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ActivityLogRepositoryInterface extends BaseRepositoryInterface
{
    public function listByModelAndId(string $model, mixed $id, array $params = []): LengthAwarePaginator;
}