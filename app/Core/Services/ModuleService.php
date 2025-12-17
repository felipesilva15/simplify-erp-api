<?php

namespace App\Core\Services;

use App\Core\Exceptions\NotFoundHttpException;
use App\Core\DTO\ModuleDTO;
use App\Core\Models\Module;
use App\Core\Repositories\Interfaces\ModuleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ModuleService
{
    public function __construct(protected ModuleRepositoryInterface $repository) { }

    public function store(ModuleDTO $data): Module {
        return $this->repository->store($data);
    }

    public function edit(Module $module): Module {
        return $module;
    }

    public function update(Module $module, ModuleDTO $data): Module {
        $module = $this->repository->update($module, $data);

        return $module;
    }

    public function delete(Module $module): bool {
        return $this->repository->delete($module);
    }

    public function show(Module $module): Module {
        return $module;
    }

    public function findById(int $id): Module {
        $module = $this->repository->findById($id);

        if (!$module) {
            throw new NotFoundHttpException();
        }

        return $module;
    }

    public function list(array $filters = []): LengthAwarePaginator {
        return $this->repository->list($filters);
    }
}