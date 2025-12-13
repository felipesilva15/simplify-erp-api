<?php

namespace App\Modules\Auth\Repositories\Interfaces;

use App\Modules\Auth\DTO\PermissionDTO;
use App\Modules\Auth\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PermissionRepositoryInterface
{
    public function store(PermissionDTO $data): Permission;

    public function update(int $id, PermissionDTO $data): ?Permission;

    public function delete(int $id): bool;

    public function findById(int $id): ?Permission;

    public function list(array $filters = []): LengthAwarePaginator;

    public function findByGroupAndAction(string $group, string $action): ?Permission;
}