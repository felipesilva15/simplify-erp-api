<?php

namespace App\Modules\Catalog\Enums;

use App\Core\Enums\ProductApplicabilityEnum;
use App\Core\Enums\ProductTypeEnum;

/**
 * @OA\Schema(
 *   schema="ItemTypeCodeEnum",
 *   type="string",
 *   description="Item type codes: 00 = Merchandise for resale; 01 = Raw material; 02 = Packaging; 03 = Work in progress; 04 = Finished product; 05 = By-product; 06 = Intermediate product; 07 = Consumable supplies; 08 = Fixed asset; 09 = Service; 10 = Other inputs; 99 = Other",
 *   enum={"00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "99"}
 * )
 */
enum ItemTypeCodeEnum: string
{
    case MerchandiseForResale = '00';
    case RawMaterial = '01';
    case Packaging = '02';
    case WorkInProgress = '03';
    case FinishedProduct = '04';
    case ByProduct = '05';
    case IntermediateProduct = '06';
    case ConsumableSupplies = '07';
    case FixedAsset = '08';
    case Service = '09';
    case OtherInputs = '10';
    case Other = '99';

    public function label(): string
    {
        return match ($this) {
            self::MerchandiseForResale => 'Mercadoria para revenda',
            self::RawMaterial => 'Matéria-prima',
            self::Packaging => 'Embalagem',
            self::WorkInProgress => 'Produto em processo',
            self::FinishedProduct => 'Produto acabado',
            self::ByProduct => 'Subproduto',
            self::IntermediateProduct => 'Produto intermediário',
            self::ConsumableSupplies => 'Material de uso e consumo',
            self::FixedAsset => 'Ativo imobilizado',
            self::Service => 'Serviços',
            self::OtherInputs => 'Outros insumos',
            self::Other => 'Outras',
        };
    }

    public function applicability(): ProductApplicabilityEnum
    {
        return match ($this) {
            self::Service => ProductApplicabilityEnum::Service,
            self::OtherInputs, self::Other => ProductApplicabilityEnum::Both,
            default => ProductApplicabilityEnum::Product,
        };
    }

    public function controlsStock(): bool
    {
        return $this->applicability() !== ProductApplicabilityEnum::Service
            && $this !== self::FixedAsset;
    }

    /**
     * Códigos permitidos para cada fachada do catálogo.
     *
     * Produto aceita os códigos exclusivos de produto (00 a 08); serviço aceita
     * os códigos de serviço e os aplicáveis a ambos (09, 10 e 99).
     */
    public static function valuesFor(ProductTypeEnum $type): array
    {
        return array_values(array_map(
            fn (self $case): string => $case->value,
            array_filter(
                self::cases(),
                fn (self $case): bool => $type === ProductTypeEnum::Service
                    ? $case->applicability() !== ProductApplicabilityEnum::Product
                    : $case->applicability() === ProductApplicabilityEnum::Product
            )
        ));
    }
}
