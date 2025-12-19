<?php

namespace App\Modules\Security\Repositories\Interfaces;

use App\Modules\Security\DTO\RoleDTO;
use App\Modules\Security\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RoleRepositoryInterface
{
    public function store(RoleDTO $data): Role;

    public function update(int $id, RoleDTO $data): ?Role;

    public function delete(int $id): bool;

    public function findById(int $id): ?Role;

    public function list(array $filters = []): LengthAwarePaginator;

    public function syncPermissions(int $id, array $permissionIds): ?Role;
}