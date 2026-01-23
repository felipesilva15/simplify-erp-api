<?php

namespace App\Core\Services;

use App\Core\Exceptions\NotFoundHttpException;
use App\Core\DTO\ModuleDTO;
use App\Core\DTO\ServiceResult;
use App\Core\Models\Module;
use App\Core\Repositories\Interfaces\ModuleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ModuleService
{
    public function __construct(protected ModuleRepositoryInterface $repository) { }

    public function store(ModuleDTO $data): ServiceResult {
        $module = $this->repository->store($data);

        return new ServiceResult(
            data: $module
        );
    }

    public function edit(Module $module): ServiceResult {
        $editable =  true;
        $warnings = [];

        if (!$module->is_active) {
            $editable = false;
            $warnings[] = 'Este módulo está não está ativo.';
        }

        return new ServiceResult(
            data: $module,
            warnings: $warnings,
            meta: [
                'editable' => $editable
            ]
        );
    }

    public function update(Module $module, ModuleDTO $data): ServiceResult {
        $module = $this->repository->update($module, $data);

        return new ServiceResult(
            data: $module
        );
    }

    public function delete(Module $module): ServiceResult {
        return new ServiceResult(
            data: null,
            meta: [
                'deleted' => $this->repository->delete($module)
            ]
        );
    }

    public function list(array $filters = []): ServiceResult {
        return new ServiceResult(
            data: $this->repository->list($filters)
        );
    }

    public function show(Module $module): ServiceResult {
        return new ServiceResult(
            data: $module
        );
    }
}