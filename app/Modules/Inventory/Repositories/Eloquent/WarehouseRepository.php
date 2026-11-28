<?php

namespace App\Modules\Inventory\Repositories\Eloquent;

use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Inventory\Repositories\Interfaces\WarehouseRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class WarehouseRepository extends BaseRepository implements WarehouseRepositoryInterface
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
    protected function getLookupFilterColumns(): array
    {
        return [
            'name',
            'is_active'
        ];
    }

    #[Override]
    protected function getModelClass(): string
    {
        return Warehouse::class;
    }
}