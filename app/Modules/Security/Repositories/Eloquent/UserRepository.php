<?php

namespace App\Modules\Security\Repositories\Eloquent;

use App\Modules\Security\Models\User;
use App\Modules\Security\Repositories\Interfaces\UserRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    #[Override]
    public function getMaskedSearchableColumns(): array
    {
        return [
            'phone_number'
        ];
    }

    #[Override]
    public function getLookupColumnsToFilter(): array
    {
        return [
            'id' => 'int',
            'name' => 'string',
            'email' => 'string',
            'username' => 'string'
        ];
    }

    #[Override]
    protected function getModelClass(): string
    {
        return User::class;
    }
}