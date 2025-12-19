<?php

namespace App\Modules\Security\Repositories\Interfaces;

use App\Modules\Security\DTO\UserDTO;
use App\Modules\Security\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function store(UserDTO $data): User;

    public function update(int $id, UserDTO $data): ?User;

    public function delete(int $id): bool;

    public function findById(int $id): ?User;

    public function list(array $filters = []): LengthAwarePaginator;

    public function syncRoles(int $id, array $roleIds): ?User;
}