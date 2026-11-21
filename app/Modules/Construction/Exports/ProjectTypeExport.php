<?php

namespace App\Modules\Construction\Exports;

use App\Core\Exports\BaseExport;

class ProjectTypeExport extends BaseExport
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