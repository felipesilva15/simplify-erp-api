<?php

namespace App\Modules\Finance\Exports;

use App\Core\Exports\BaseExport;

class FinanceCategoryExport extends BaseExport
{
    public function headings(): array
    {
        return [
            'ID',
            'Categoria pai',
            'Código',
            'Nome',
            'Aplicável a'
        ];
    }

    public function map(mixed $row): array
    {
        return [
            $row->id,
            $row->parent_id,
            $row->code,
            $row->name,
            $row->applicable_type
        ];
    }
}