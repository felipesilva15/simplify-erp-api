<?php

namespace App\Core\Actions\Module;

use App\Core\DTO\ModuleDTO;
use App\Core\Services\ModuleService;
use App\Core\Models\Module;

class UpdateModuleAction
{
    public function __construct(protected ModuleService $service) { }

    public function execute(int $id, ModuleDTO $data): Module {
        return $this->service->update($id, $data);
    }
}