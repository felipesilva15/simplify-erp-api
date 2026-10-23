<?php

namespace App\Modules\Catalog\Repositories\Eloquent;

use App\Modules\Catalog\Models\ProductBrand;
use App\Modules\Catalog\Repositories\Interfaces\ProductBrandRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class ProductBrandRepository extends BaseRepository implements ProductBrandRepositoryInterface
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
        return ProductBrand::class;
    }
}