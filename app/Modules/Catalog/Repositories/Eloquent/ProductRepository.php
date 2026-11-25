<?php

namespace App\Modules\Catalog\Repositories\Eloquent;

use App\Core\Enums\ProductTypeEnum;
use App\Core\Repositories\Eloquent\BaseRepository;
use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Repositories\Interfaces\ProductRepositoryInterface;
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
    protected function getLookupFilterColumns(): array
    {
        return [
            'name',
            'sku',
            'gtin',
            'item_type_code',
            'is_active',
            'product_category_id',
            'product_brand_id',
            'measurement_unit_id',
        ];
    }

    /**
     * Products e services compartilham a mesma tabela, mas `gtin` só faz sentido
     * na fachada de products; services não expõe o campo no LookupResource.
     */
    #[Override]
    protected function getLookupFilterExcludedColumns(array $params): array
    {
        return ($params['forcedFilters']['type'] ?? null) === ProductTypeEnum::Service->value
            ? ['gtin']
            : [];
    }

    #[Override]
    protected function getModelClass(): string
    {
        return Product::class;
    }
}