<?php 

namespace App\Core\Enums;

enum ActivityActionEnum: string
{
    case Created   = 'created';
    case Updated   = 'updated';
    case Deleted   = 'deleted';
    case Approved  = 'approved';

    public function label(): string
    {
        return match ($this) {
            self::Created   => 'Criado',
            self::Updated   => 'Atualizado',
            self::Deleted   => 'Excluído',
            self::Approved  => 'Aprovado',
        };
    }
}