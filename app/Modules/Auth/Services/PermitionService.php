<?php

namespace App\Modules\Auth\Services;

use App\Core\Exceptions\NotFoundHttpException;
use App\Modules\Auth\DTO\PermitionDTO;
use App\Modules\Auth\Models\Permition;
use App\Modules\Auth\Repositories\Interfaces\PermitionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PermitionService
{
    public function __construct(protected PermitionRepositoryInterface $repository) { }

    public function store(PermitionDTO $data): Permition {
        return $this->repository->store($data);
    }

    public function edit(int $id): ?Permition {
        $permition = $this->repository->findById($id);

        if (!$permition) {
            throw new NotFoundHttpException();
        }

        return $permition;
    }

    public function update(int $id, PermitionDTO $data): ?Permition {
        $permition = $this->repository->update($id, $data);

        if (!$permition) {
            throw new NotFoundHttpException();
        }

        return $permition;
    }

    public function delete(int $id): bool {
        return $this->repository->delete($id);
    }

    public function findById(int $id): ?Permition {
        $permition = $this->repository->findById($id);

        if (!$permition) {
            throw new NotFoundHttpException();
        }

        return $permition;
    }

    public function list(array $filters = []): LengthAwarePaginator {
        return $this->repository->list($filters);
    }
}