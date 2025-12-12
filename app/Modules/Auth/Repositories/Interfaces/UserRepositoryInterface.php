<?php

namespace App\Modules\Auth\Repositories\Interfaces;

use App\Modules\Auth\DTO\UserDTO;
use App\Modules\Auth\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function store(UserDTO $data): User;

    public function update(int $id, UserDTO $data): ?User;

    public function delete(int $id): bool;

    public function findById(int $id): ?User;

    public function list(array $filters = []): LengthAwarePaginator;
}