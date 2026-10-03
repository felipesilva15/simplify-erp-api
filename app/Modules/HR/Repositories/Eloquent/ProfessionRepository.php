<?php

namespace App\Modules\HR\Repositories\Eloquent;

use App\Modules\HR\Models\Profession;
use App\Modules\HR\Repositories\Interfaces\ProfessionRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class ProfessionRepository extends BaseRepository implements ProfessionRepositoryInterface
{
    #[Override]
    protected function getListColumnsToFilter(): array
    {
        return [
            'id',
            'cbo',
            'name'
        ];
    }

    #[Override]
    protected function getLookupColumnsToFilter(): array
    {
        return [
            'cbo',
            'name'
        ];
    }

    #[Override]
    protected function getModelClass(): string
    {
        return Profession::class;
    }
}