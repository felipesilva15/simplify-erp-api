<?php

namespace App\Modules\Security\Repositories\Interfaces;

use App\Modules\Security\DTO\PermissionDTO;
use App\Modules\Security\Models\Permission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PermissionRepositoryInterface
{
    public function store(PermissionDTO $data): Permission;

    public function update(int $id, PermissionDTO $data): ?Permission;

    public function delete(int $id): bool;

    public function findById(int $id): ?Permission;

    public function list(array $filters = []): LengthAwarePaginator;

    public function findByResourceAndAction(string $resource, string $action): ?Permission;
}