<?php

namespace App\Core\Enums;

/**
 * @OA\Schema(
 *   schema="ProductTypeEnum",
 *   type="string",
 *   description="Product types: PRODUCT, SERVICE",
 *   enum={"PRODUCT", "SERVICE"}
 * )
 */
enum ProductTypeEnum: string
{
    case Product = 'PRODUCT';
    case Service = 'SERVICE';

    public function label(): string
    {
        return match ($this) {
            self::Product => 'Produto',
            self::Service => 'Serviço',
        };
    }
}
