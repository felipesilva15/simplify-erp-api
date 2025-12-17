<?php

namespace App\Core\Repositories\Interfaces;

use App\Core\DTO\ModuleDTO;
use App\Core\Models\Module;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ModuleRepositoryInterface
{
    public function store(ModuleDTO $data): Module;

    public function update(Module $module, ModuleDTO $data): ?Module;

    public function delete(Module $module): bool;

    public function findById(int $id): ?Module;

    public function list(array $filters = []): LengthAwarePaginator;
}