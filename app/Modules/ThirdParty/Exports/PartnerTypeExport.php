<?php

namespace App\Modules\ThirdParty\Exports;

use App\Core\Exports\BaseExport;

class PartnerTypeExport extends BaseExport
{
    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'Código'
        ];
    }

    public function map(mixed $row): array
    {
        return [
            $row->id,
            $row->name,
            $row->code,
        ];
    }
}