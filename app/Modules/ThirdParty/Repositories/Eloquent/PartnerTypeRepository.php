<?php

namespace App\Modules\ThirdParty\Repositories\Eloquent;

use App\Modules\ThirdParty\Models\PartnerType;
use App\Modules\ThirdParty\Repositories\Interfaces\PartnerTypeRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class PartnerTypeRepository extends BaseRepository implements PartnerTypeRepositoryInterface
{
    #[Override]
    protected function getListColumnsToFilter(): array
    {
        return [
            'id',
            'name',
            'code'
        ];
    }

    #[Override]
    protected function getLookupColumnsToFilter(): array
    {
        return [
            'name',
            'code'
        ];
    }

    #[Override]
    protected function getLookupKeyColumn(): string
    {
        return 'code';
    }

    #[Override]
    protected function getModelClass(): string
    {
        return PartnerType::class;
    }
}