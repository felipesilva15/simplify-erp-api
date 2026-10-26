<?php

namespace App\Modules\Catalog\Repositories\Eloquent;

use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Repositories\Interfaces\ProductRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    #[Override]
    protected function withRelations(): array
    {
        return [
            'measurementUnit',
            'category',
            'brand'
        ];
    }

    #[Override]
    protected function getListColumnsToFilter(): array
    {
        return [
            'id',
            'sku',
            'gtin',
            'name'
        ];
    }

    #[Override]
    protected function getLookupColumnsToFilter(): array
    {
        return [
            'id',
            'sku',
            'gtin',
            'name',
        ];
    }

    #[Override]
    protected function getModelClass(): string
    {
        return Product::class;
    }
}