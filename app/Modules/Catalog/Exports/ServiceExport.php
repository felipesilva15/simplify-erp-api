<?php

namespace App\Modules\Catalog\Exports;

use App\Core\Exports\BaseExport;

class ServiceExport extends BaseExport
{
    public function headings(): array
    {
        return [
            'ID',
            'Tipo',
            'Tipo de item (SPED)',
            'Código',
            'Nome',
            'Descrição',
            'Observações',
            'Preço',
            'Custo',
            'Ativo',
            'ID da Unidade de Medida',
            'ID da Categoria do Produto',
        ];
    }

    public function map(mixed $row): array
    {
        return [
            $row->id,
            $row->type,
            $row->item_type_code,
            $row->sku,
            $row->name,
            $row->description,
            $row->notes,
            $row->price,
            $row->cost,
            $row->is_active,
            $row->measurement_unit_id,
            $row->product_category_id,
        ];
    }
}
