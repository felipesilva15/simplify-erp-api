<?php 

namespace App\Core\Enums;

/**
 * @OA\Schema(
 *   schema="ActivityActionEnum",
 *   type="string",
 *   description="Activity actions:
 *      Created = 'CREATED'
 *      Updated = 'UPDATED'
 *      Deleted = 'DELETED'
 *      Approved = 'APPROVED'
 *      Auth = 'AUTH'",
 *   enum={"CREATED", "UPDATED", "DELETED", "APPROVED", "AUTH"}
 * )
 */
enum ActivityActionEnum: string
{
    case Created   = 'CREATED';
    case Updated   = 'UPDATED';
    case Deleted   = 'DELETED';
    case Approved  = 'APPROVED';
    case Auth  = 'AUTH';

    public function label(): string
    {
        return match ($this) {
            self::Created   => 'Criado',
            self::Updated   => 'Atualizado',
            self::Deleted   => 'Excluído',
            self::Approved  => 'Aprovado',
            self::Auth  => 'Autenticado',
        };
    }
}