<?php

namespace App\Modules\Security\Exports;

use App\Core\Exports\BaseExport;

class RoleExport extends BaseExport
{
    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'Descrição'
        ];
    }

    public function map(mixed $row): array
    {
        return [
            $row->id,
            $row->name,
            $row->description
        ];
    }
}