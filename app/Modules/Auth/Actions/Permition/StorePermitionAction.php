<?php

namespace App\Modules\Auth\Actions\Permition;

use App\Modules\Auth\DTO\PermitionDTO;
use App\Modules\Auth\Services\PermitionService;
use App\Modules\Auth\Models\Permition;

class StorePermitionAction
{
    public function __construct(protected PermitionService $service) { }

    public function execute(PermitionDTO $data): Permition {
        return $this->service->store($data);
    }
}