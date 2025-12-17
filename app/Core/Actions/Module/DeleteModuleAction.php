<?php

namespace App\Core\Actions\Module;

use App\Core\Models\Module;
use App\Core\Services\ModuleService;

class DeleteModuleAction
{
    public function __construct(protected ModuleService $service) {}

    public function execute(Module $module): bool {
        return $this->service->delete($module);
    }
}