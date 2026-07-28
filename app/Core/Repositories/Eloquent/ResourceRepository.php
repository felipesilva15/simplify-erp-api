<?php

namespace App\Core\Repositories\Eloquent;

use App\Core\Models\Resource;
use App\Core\Repositories\Interfaces\ResourceRepositoryInterface;
use Override;

class ResourceRepository extends BaseRepository implements ResourceRepositoryInterface
{
    #[Override]
    protected function getModelClass(): string
    {
        return Resource::class;
    }
}