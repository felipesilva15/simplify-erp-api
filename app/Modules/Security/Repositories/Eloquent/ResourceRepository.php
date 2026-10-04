<?php

namespace App\Modules\Security\Repositories\Eloquent;

use App\Modules\Security\Models\Resource;
use App\Modules\Security\Repositories\Interfaces\ResourceRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class ResourceRepository extends BaseRepository implements ResourceRepositoryInterface
{
    #[Override]
    protected function getModelClass(): string
    {
        return Resource::class;
    }
}
