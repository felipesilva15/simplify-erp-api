<?php

namespace App\Modules\Security\Repositories\Eloquent;

use App\Modules\Security\Models\User;
use App\Modules\Security\Repositories\Interfaces\UserRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    #[Override]
    protected function getModelClass(): string
    {
        return User::class;
    }
}