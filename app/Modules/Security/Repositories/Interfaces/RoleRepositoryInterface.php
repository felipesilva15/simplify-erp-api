<?php

namespace App\Modules\Security\Repositories\Interfaces;

use App\Modules\Security\DTO\RoleDTO;
use App\Modules\Security\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RoleRepositoryInterface
{
    public function store(RoleDTO $data): Role;

    public function update(Role $role, RoleDTO $data): ?Role;

    public function delete(Role $role): bool;

    public function list(array $filters = []): LengthAwarePaginator;

    public function syncPermissions(Role $role, array $permissionIds): ?Role;
}