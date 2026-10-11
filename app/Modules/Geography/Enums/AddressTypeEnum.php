<?php

namespace App\Modules\Geography\Enums;

/**
 * @OA\Schema(
 *   schema="AddressTypeEnum",
 *   type="string",
 *   description="Tipos de endereço:
 *      Fiscal = 'TAX'
 *      Entrega = 'DELIVERY'
 *      Cobrança = 'BILLING'",
 *   enum={"TAX", "DELIVERY", "BILLING"}
 * )
 */
enum AddressTypeEnum: string
{
    case Tax = 'TAX';
    case Delivery = 'DELIVERY';
    case Billing = 'BILLING';

    public function label(): string
    {
        return match ($this) {
            self::Tax => 'Fiscal',
            self::Delivery => 'Entrega',
            self::Billing => 'Cobrança',
        };
    }
}
