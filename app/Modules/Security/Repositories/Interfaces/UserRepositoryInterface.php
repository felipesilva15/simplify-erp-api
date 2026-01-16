<?php

namespace App\Modules\Security\Repositories\Interfaces;

use App\Modules\Security\DTO\UserDTO;
use App\Modules\Security\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function store(UserDTO $data): User;

    public function update(User $user, UserDTO $data): ?User;

    public function delete(User $user): bool;

    public function list(array $filters = []): LengthAwarePaginator;

    public function syncRoles(User $user, array $roleIds): ?User;
}