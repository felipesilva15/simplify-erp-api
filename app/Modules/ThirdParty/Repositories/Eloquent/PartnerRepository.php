<?php

namespace App\Modules\ThirdParty\Repositories\Eloquent;

use App\Modules\ThirdParty\Models\Partner;
use App\Modules\ThirdParty\Repositories\Interfaces\PartnerRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class PartnerRepository extends BaseRepository implements PartnerRepositoryInterface
{
    #[Override]
    protected function withRelations(): array
    {
        return [
            'contacts'
        ];
    }

    #[Override]
    public function getMaskedSearchableColumns(): array
    {
        return [
            'document_number'
        ];
    }

    #[Override]
    protected function getListColumnsToFilter(): array
    {
        return [
            'id',
            'name',
            'trade_name',
            'document_number'
        ];
    }

    #[Override]
    protected function getLookupColumnsToFilter(): array
    {
        return [
            'id',
            'name',
            'trade_name',
            'document_number'
        ];
    }

    #[Override]
    protected function getModelClass(): string
    {
        return Partner::class;
    }
}