<?php

namespace App\Core\Repositories\Eloquent;

use App\Core\Models\Module;
use App\Core\Repositories\Interfaces\ModuleRepositoryInterface;
use Override;

class ModuleRepository extends BaseRepository implements ModuleRepositoryInterface
{
    #[Override]
    protected function getModelClass(): string
    {
        return Module::class;
    }
}