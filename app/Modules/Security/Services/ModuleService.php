<?php

namespace App\Modules\Security\Services;

use App\Core\Services\ActivityLogService;
use App\Core\Services\BaseCrudService;
use App\Modules\Security\Repositories\Interfaces\ModuleRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class ModuleService extends BaseCrudService
{
    public function __construct(ModuleRepositoryInterface $repository, ActivityLogService $activity)
    {
        $this->repository = $repository;
        $this->activity = $activity;
    }

    protected function canEdit(Model $module): bool
    {
        return (bool) $module->is_active;
    }

    protected function editWarnings(Model $module): array
    {
        return $module->is_active ? [] : ['Este módulo não está ativo.'];
    }
}
