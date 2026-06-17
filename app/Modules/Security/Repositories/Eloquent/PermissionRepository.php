<?php

namespace App\Modules\Security\Repositories\Eloquent;

use App\Modules\Security\Models\Permission;
use App\Modules\Security\Repositories\Interfaces\PermissionRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    #[Override]
    protected function getModelClass(): string
    {
        return Permission::class;
    }
}