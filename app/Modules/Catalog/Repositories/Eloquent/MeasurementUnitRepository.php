<?php

namespace App\Modules\Catalog\Repositories\Eloquent;

use App\Modules\Catalog\Models\MeasurementUnit;
use App\Modules\Catalog\Repositories\Interfaces\MeasurementUnitRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class MeasurementUnitRepository extends BaseRepository implements MeasurementUnitRepositoryInterface
{
    #[Override]
    protected function getListColumnsToFilter(): array
    {
        return [
            'id',
            'code',
            'name',
            'symbol',
            'category'
        ];
    }

    #[Override]
    protected function getLookupColumnsToFilter(): array
    {
        return [
            'id',
            'code',
            'name',
            'symbol',
            'category'
        ];
    }

    #[Override]
    protected function getLookupKeyColumn(): string
    {
        return 'id';
    }

    #[Override]
    protected function getModelClass(): string
    {
        return MeasurementUnit::class;
    }
}