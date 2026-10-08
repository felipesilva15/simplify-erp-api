<?php

namespace App\Modules\Catalog\Exports;

use App\Core\Exports\BaseExport;

class ProductCategoryExport extends BaseExport
{
    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'ID categoria pai',
            'Nome categoria pai',
            'Aplicabilidade',
            'Ativo'
        ];
    }

    public function map(mixed $row): array
    {
        return [
            $row->id,
            $row->name,
            $row->parentCategory?->id ?? '',
            $row->parentCategory?->name ?? '',
            $row->applicability?->label(),
            $row->is_active ? 'Sim' : 'Não'
        ];
    }
}