<?php

namespace App\Core\Actions\Module;

use App\Core\DTO\ServiceResult;
use App\Core\Services\ModuleService;
use Illuminate\Pagination\LengthAwarePaginator;

class ListModuleAction
{
    public function __construct(protected ModuleService $service) {}

    public function execute(array $filters): ServiceResult {
        return $this->service->list($filters);
    }
}