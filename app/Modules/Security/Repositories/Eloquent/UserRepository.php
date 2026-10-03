<?php

namespace App\Modules\Security\Repositories\Eloquent;

use App\Modules\Security\Models\User;
use App\Modules\Security\Repositories\Interfaces\UserRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    #[Override]
    protected function withRelations(): array
    {
        return [
            'roles'
        ];
    }

    #[Override]
    public function getMaskedSearchableColumns(): array
    {
        return [
            'phone_number'
        ];
    }

    #[Override]
    protected function getListColumnsToFilter(): array
    {
        return [
            'id',
            'name',
            'email',
            'username'
        ];
    }

    #[Override]
    protected function getLookupColumnsToFilter(): array
    {
        return [
            'id',
            'name',
            'email',
            'username'
        ];
    }

    #[Override]
    protected function getModelClass(): string
    {
        return User::class;
    }
}