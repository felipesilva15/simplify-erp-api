<?php

namespace App\Modules\Auth\Repositories\Interfaces;

use App\Modules\Auth\DTO\RoleDTO;
use App\Modules\Auth\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RoleRepositoryInterface
{
    public function store(RoleDTO $data): Role;

    public function update(int $id, RoleDTO $data): ?Role;

    public function delete(int $id): bool;

    public function findById(int $id): ?Role;

    public function list(array $filters = []): LengthAwarePaginator;
}