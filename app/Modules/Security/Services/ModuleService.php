<?php

namespace App\Modules\Security\Services;

use App\Core\DTO\ServiceResult;
use App\Core\Services\BaseCrudService;
use App\Core\Services\ActivityLogService;
use App\Modules\Security\Repositories\Interfaces\ModuleRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Override;

class ModuleService extends BaseCrudService
{
    public function __construct(ModuleRepositoryInterface $repository, ActivityLogService $activity)
    {
        $this->repository = $repository;
        $this->activity = $activity;
    }

    #[Override]
    public function edit(Model $module): ServiceResult {
       $result = parent::edit($module);

        if (!$module->is_active) {
            $result->meta['editable'] = false;
            $result->warnings[] = 'Este módulo está não está ativo.';
        }

        return $result;
    }
}
