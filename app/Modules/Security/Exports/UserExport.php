<?php

namespace App\Modules\Security\Exports;

use App\Core\Exports\BaseExport;

class UserExport extends BaseExport
{
    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'E-mail',
            'Usuário',
            'Telefone',
            'Administrador',
            'Perfis'
        ];
    }

    public function map(mixed $row): array
    {
        
        return [
            $row->id,
            $row->name,
            $row->email,
            $row->username,
            $row->phone_number,
            $row->is_admin ? 'Sim' : 'Não',
            $row->roles->pluck('name')->implode(', '),
        ];
    }
}