<?php 

namespace App\Core\Enums;

/**
 * @OA\Schema(
 *   schema="ProductApplicabilityEnum",
 *   type="string",
 *   description="Product applicabilities:
 *      Product = 'PRODUCT'
 *      Service = 'SERVICE'
 *      Both = 'BOTH'"
 *   enum={"PRODUCT", "SERVICE", "BOTH"}
 * )
 */
enum ProductApplicabilityEnum: string
{
    case Product    = 'PRODUCT';
    case Service    = 'SERVICE';
    case Both       = 'BOTH';

    public function label(): string
    {
        return match ($this) {
            self::Product   => 'Produto',
            self::Service   => 'Serviço',
            self::Both      => 'Ambos',
        };
    }
}