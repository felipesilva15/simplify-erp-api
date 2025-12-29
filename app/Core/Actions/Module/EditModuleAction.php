<?php

namespace App\Core\Actions\Module;

use App\Core\DTO\ServiceResult;
use App\Core\Services\ModuleService;
use App\Core\Models\Module;

class EditModuleAction
{
    public function __construct(protected ModuleService $service) { }

    public function execute(Module $module): ServiceResult {
        return $this->service->edit($module);
    }
}