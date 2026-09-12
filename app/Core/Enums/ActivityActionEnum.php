<?php 

namespace App\Core\Enums;

/**
 * @OA\Schema(
 *   schema="ActivityActionEnum",
 *   type="string",
 *   description="Activity actions:
 *      Created = 'created'
 *      Updated = 'updated'
 *      Deleted = 'deleted'
 *      Approved = 'approved'
 *      Auth = 'auth'",
 *   enum={"created", "updated", "deleted", "approved", "auth"}
 * )
 */
enum ActivityActionEnum: string
{
    case Created   = 'created';
    case Updated   = 'updated';
    case Deleted   = 'deleted';
    case Approved  = 'approved';
    case Auth  = 'auth';

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