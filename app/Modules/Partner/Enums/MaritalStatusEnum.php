<?php 

namespace App\Modules\Partner\Enums;

/**
 * @OA\Schema(
 *   schema="MaritalStatusEnum",
 *   type="string",
 *   description="Marital status",
 *   enum={"MARIED", "WIDOWED", "SINGLE", "SEPARATED", "DIVORCED"}
 * )
 */
enum MaritalStatusEnum: string
{
    case Maried     = 'MARIED';
    case Widowed    = 'WIDOWED';
    case Single     = 'SINGLE';
    case Separated  = 'SEPARATED';
    case Divorced   = 'DIVORCED';

    public function label(): string
    {
        return match ($this) {
            self::Maried    => 'Casado(a)',
            self::Widowed   => 'Viúvo(a)',
            self::Single    => 'Solteiro(a)',
            self::Separated => 'Separado(a)',
            self::Divorced  => 'Divorciado(a)',
        };
    }
}