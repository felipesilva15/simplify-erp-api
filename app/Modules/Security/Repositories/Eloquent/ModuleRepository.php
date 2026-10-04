<?php

namespace App\Modules\Security\Repositories\Eloquent;

use App\Modules\Security\Models\Module;
use App\Modules\Security\Repositories\Interfaces\ModuleRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class ModuleRepository extends BaseRepository implements ModuleRepositoryInterface
{
    #[Override]
    protected function getModelClass(): string
    {
        return Module::class;
    }
}
