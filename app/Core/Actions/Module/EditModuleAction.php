<?php

namespace App\Core\Actions\Module;

use App\Core\DTO\ModuleDTO;
use App\Core\Services\ModuleService;
use App\Core\Models\Module;

class EditModuleAction
{
    public function __construct(protected ModuleService $service) { }

    public function execute(Module $module): Module {
        return $this->service->edit($module);
    }
}