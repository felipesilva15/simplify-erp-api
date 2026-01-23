<?php

namespace App\Core\Actions\Module;

use App\Core\DTO\ServiceResult;
use App\Core\Models\Module;
use App\Core\Services\ModuleService;

class DeleteModuleAction
{
    public function __construct(protected ModuleService $service) {}

    public function execute(Module $module): ServiceResult {
        return $this->service->delete($module);
    }
}