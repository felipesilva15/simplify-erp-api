<?php

namespace App\Modules\Security\Repositories\Eloquent;

use App\Modules\Security\Models\Role;
use App\Modules\Security\Repositories\Interfaces\RoleRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    #[Override]
    protected function getListColumnsToFilter(): array
    {
        return [
            'id',
            'name'
        ];
    }

    #[Override]
    protected function getLookupColumnsToFilter(): array
    {
        return [
            'id',
            'name'
        ];
    }

    #[Override]
    public function getModelClass(): string
    {
        return Role::class;
    }
}