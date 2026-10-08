<?php

namespace App\Modules\Catalog\Repositories\Eloquent;

use App\Modules\Catalog\Models\ProductCategory;
use App\Modules\Catalog\Repositories\Interfaces\ProductCategoryRepositoryInterface;
use App\Core\Repositories\Eloquent\BaseRepository;
use Override;

class ProductCategoryRepository extends BaseRepository implements ProductCategoryRepositoryInterface
{
    #[Override]
    protected function withRelations(): array
    {
        return [
            'subcategories',
        ];
    }

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
        return ProductCategory::class;
    }
}