<?php

namespace App\Modules\Auth\Actions\Permition;

use App\Modules\Auth\Services\PermitionService;

class DeletePermitionAction
{
    public function __construct(protected PermitionService $service) {}

    public function execute(int $id): bool {
        return $this->service->delete($id);
    }
}