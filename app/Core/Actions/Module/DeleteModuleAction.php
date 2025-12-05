<?php

namespace App\Core\Actions\Module;

use App\Core\Services\ModuleService;

class DeleteModuleAction
{
    public function __construct(protected ModuleService $service) {}

    public function execute(int $id): bool {
        return $this->service->delete($id);
    }
}