<?php

namespace App\Modules\Catalog\Exports;

use App\Core\Exports\BaseExport;

class ProductExport extends BaseExport
{
    public function headings(): array
    {
        return [
            'ID',
            'Tipo',
            'Tipo de item (SPED)',
            'SKU',
            'GTIN',
            'Nome',
            'Descrição',
            'Observações',
            'Preço',
            'Custo',
            'Peso Líquido',
            'Peso Bruto',
            'Largura',
            'Altura',
            'Comprimento',
            'Estoque Mínimo',
            'Estoque Máximo',
            'Ativo',
            'ID da Unidade de Medida',
            'ID da Categoria do Produto',
            'ID da Marca do Produto'
        ];
    }

    public function map(mixed $row): array
    {
        return [
            $row->id,
            $row->type,
            $row->item_type_code,
            $row->sku,
            $row->gtin,
            $row->name,
            $row->description,
            $row->notes,
            $row->price,
            $row->cost,
            $row->net_weight,
            $row->gross_weight,
            $row->width,
            $row->height,
            $row->length,
            $row->minimum_stock,
            $row->maximum_stock,
            $row->is_active,
            $row->measurement_unit_id,
            $row->product_category_id,
            $row->product_brand_id
        ];
    }
}