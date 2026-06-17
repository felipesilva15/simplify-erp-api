<?php

namespace App\Core\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function list(array $filters = []): LengthAwarePaginator;

    public function getById(mixed $id): ?Model;

    public function store(mixed $data): Model;

    public function update(Model $entity, mixed $data): ?Model;

    public function delete(Model $entity): bool;

    public function lookup(array $params = []): LengthAwarePaginator;

    public function sync(Model $entity, string $relationMethodName, array $ids = []): ?Model;
}