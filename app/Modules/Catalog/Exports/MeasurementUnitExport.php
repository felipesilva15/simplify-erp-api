<?php

namespace App\Modules\Catalog\Exports;

use App\Core\Exports\BaseExport;

class MeasurementUnitExport extends BaseExport
{
    public function headings(): array
    {
        return [
            'ID',
            'Código',
            'Nome',
            'Símbolo',
            'Categoria',
            'ID da Unidade base',
            'Fator de conversão',
            'Casas decimais',
            'Código externo',
            'Sistema',
            'Ativo'
        ];
    }

    public function map(mixed $row): array
    {
        return [
            $row->id,
            $row->code,
            $row->name,
            $row->symbol,
            $row->category,
            $row->base_unit_id,
            $row->conversion_factor,
            $row->decimal_places,
            $row->external_code,
            $row->is_system ? 'Sim' : 'Não',
            $row->is_active ? 'Sim' : 'Não'
        ];
    }
}