<?php

namespace App\Modules\Partner\Repositories\Eloquent;

use App\Modules\Partner\Models\Partner;
use App\Modules\Partner\Repositories\Interfaces\PartnerRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class PartnerRepository extends BaseRepository implements PartnerRepositoryInterface
{
    #[Override]
    public function getLookupColumnsToFilter(): array
    {
        return [
            'id' => 'string',
            'name' => 'string',
            'trade_name' => 'string',
            'document_number' => 'string'
        ];
    }

    #[Override]
    protected function getModelClass(): string
    {
        return Partner::class;
    }
}