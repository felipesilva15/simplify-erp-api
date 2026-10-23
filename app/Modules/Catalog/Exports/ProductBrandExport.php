<?php

namespace App\Modules\Catalog\Exports;

use App\Core\Exports\BaseExport;

class ProductBrandExport extends BaseExport
{
    public function headings(): array
    {
        return [
            'ID',
            'Nome'
        ];
    }

    public function map(mixed $row): array
    {
        return [
            $row->id,
            $row->name
        ];
    }
}