<?php 

namespace App\Modules\Partner\Enums;

/**
 * @OA\Schema(
 *   schema="PersonTypeEnum",
 *   type="string",
 *   description="Person type",
 *   enum={"PERSON", "COMPANY", "FOREIGN"}
 * )
 */
enum PersonTypeEnum: string
{
    case Person     = 'PERSON';
    case Company    = 'COMPANY';
    case Foreign    = 'FOREIGN';

    public function label(): string
    {
        return match ($this) {
            self::Person    => 'Pessoa',
            self::Company   => 'Empresa',
            self::Foreign   => 'Estrangeiro',
        };
    }
}