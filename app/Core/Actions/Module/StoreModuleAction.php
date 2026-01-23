<?php

namespace App\Core\Actions\Module;

use App\Core\DTO\ModuleDTO;
use App\Core\DTO\ServiceResult;
use App\Core\Services\ModuleService;
use App\Core\Models\Module;

class StoreModuleAction
{
    public function __construct(protected ModuleService $service) { }

    public function execute(ModuleDTO $data): ServiceResult {
        return $this->service->store($data);
    }
}