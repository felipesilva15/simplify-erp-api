<?php

namespace App\Modules\HR\Repositories\Eloquent;

use App\Modules\HR\Models\Profession;
use App\Modules\HR\Repositories\Interfaces\ProfessionRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class ProfessionRepository extends BaseRepository implements ProfessionRepositoryInterface
{
    #[Override]
    public function getLookupColumnsToFilter(): array
    {
        return [
            'cbo' => 'string',
            'name' => 'string'
        ];
    }

    #[Override]
    protected function getModelClass(): string
    {
        return Profession::class;
    }
}