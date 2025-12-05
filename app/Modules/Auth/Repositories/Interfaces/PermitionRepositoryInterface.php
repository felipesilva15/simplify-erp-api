<?php

namespace App\Modules\Auth\Repositories\Interfaces;

use App\Modules\Auth\DTO\PermitionDTO;
use App\Modules\Auth\Models\Permition;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PermitionRepositoryInterface
{
    public function store(PermitionDTO $data): Permition;

    public function update(int $id, PermitionDTO $data): ?Permition;

    public function delete(int $id): bool;

    public function findById(int $id): ?Permition;

    public function list(array $filters = []): LengthAwarePaginator;
}