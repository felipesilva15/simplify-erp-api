<?php

namespace App\Modules\Construction\Repositories\Eloquent;

use App\Modules\Construction\Models\ProjectType;
use App\Modules\Construction\Repositories\Interfaces\ProjectTypeRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class ProjectTypeRepository extends BaseRepository implements ProjectTypeRepositoryInterface
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
    protected function getModelClass(): string
    {
        return ProjectType::class;
    }
}