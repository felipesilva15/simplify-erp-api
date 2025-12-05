<?php

namespace App\Modules\Auth\Actions\Permition;

use App\Modules\Auth\Services\PermitionService;
use App\Modules\Auth\Models\Permition;

class ShowPermitionAction
{
    public function __construct(protected PermitionService $service) { }

    public function execute(int $id): Permition {
        return $this->service->findById($id);
    }
}