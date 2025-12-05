<?php

namespace App\Core\Actions\Module;

use App\Core\Services\ModuleService;
use App\Core\Models\Module;

class ShowModuleAction
{
    public function __construct(protected ModuleService $service) { }

    public function execute(int $id): Module {
        return $this->service->findById($id);
    }
}