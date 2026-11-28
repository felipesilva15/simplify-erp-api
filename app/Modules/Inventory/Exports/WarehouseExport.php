<?php

namespace App\Modules\Inventory\Exports;

use App\Core\Exports\BaseExport;

class WarehouseExport extends BaseExport
{
    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'Ativo'
        ];
    }

    public function map(mixed $row): array
    {
        return [
            $row->id,
            $row->name,
            $row->is_active
        ];
    }
}