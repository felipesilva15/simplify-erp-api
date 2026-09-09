<?php 

namespace App\Modules\Partner\Enums;

/**
 * @OA\Schema(
 *   schema="TaxpayerTypeEnum",
 *   type="string",
 *   description="Taxpayer type",
 *   enum={"TAXPAYER", "NON_TAXPAYER", "EXEMPT"}
 * )
 */
enum TaxpayerTypeEnum: string
{
    case Taxpayer       = 'TAXPAYER';
    case NonTaxpayer    = 'NON_TAXPAYER';
    case Exempt         = 'EXEMPT';

    public function label(): string
    {
        return match ($this) {
            self::Taxpayer      => 'Contribuínte',
            self::NonTaxpayer   => 'Não contribuínte',
            self::Exempt        => 'Isento',
        };
    }
}