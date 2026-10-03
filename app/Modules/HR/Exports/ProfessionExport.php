<?php

namespace App\Modules\HR\Exports;

use App\Core\Exports\BaseExport;

class ProfessionExport extends BaseExport
{
    public function headings(): array
    {
        return [
            'ID',
            'CBO',
            'Nome'
        ];
    }

    public function map(mixed $row): array
    {
        return [
            $row->id,
            $row->cbo,
            $row->name
        ];
    }
}