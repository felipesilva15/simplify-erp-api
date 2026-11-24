<?php

namespace App\Modules\Finance\Enums;

/**
 * @OA\Schema(
 *   schema="TaxTypeEnum",
 *   type="string",
 *   description="Activity actions: FIXED_VALUE, ALIQUOT, FIXED_VALUE_AND_ALIQUOT",
 *   enum={"FIXED_VALUE", "ALIQUOT", "FIXED_VALUE_AND_ALIQUOT"}
 * )
 */
enum TaxTypeEnum: string
{
    case FixedValue = 'FIXED_VALUE';
    case Aliquot = 'ALIQUOT';
    case FiexedValueAndAliquot = 'FIXED_VALUE_AND_ALIQUOT';

    public function label(): string
    {
        return match ($this) {
            self::FixedValue => 'Valor fixo',
            self::Aliquot => 'Alíquota',
            self::FiexedValueAndAliquot => 'Valor fixo e alíquota',
        };
    }
}
