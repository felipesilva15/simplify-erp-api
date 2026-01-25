<?php

namespace App\Modules\Security\Repositories\Interfaces;

use App\Modules\Security\DTO\PermissionDTO;
use App\Modules\Security\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PermissionRepositoryInterface
{
    public function store(PermissionDTO $data): Permission;

    public function update(Permission $permission, PermissionDTO $data): ?Permission;

    public function delete(Permission $permission): bool;

    public function list(array $filters = []): LengthAwarePaginator;
}