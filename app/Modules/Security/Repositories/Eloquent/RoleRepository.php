<?php

namespace App\Modules\Security\Repositories\Eloquent;

use App\Modules\Security\DTO\RoleDTO;
use App\Modules\Security\Models\Role;
use App\Modules\Security\Repositories\Interfaces\RoleRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    #[Override]
    public function getLookupColumnsToFilter(): array
    {
        return [
            'id' => 'int',
            'name' => 'string'
        ];
    }

    #[Override]
    public function getModelClass(): string
    {
        return Role::class;
    }
}