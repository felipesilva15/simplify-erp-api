<?php

namespace App\Modules\Catalog\Enums;

use App\Core\Enums\ProductApplicabilityEnum;

enum ItemTypeCodeEnum: string
{
    case MerchandiseForResale = '00';
    case RawMaterial          = '01';
    case Packaging            = '02';
    case WorkInProgress       = '03';
    case FinishedProduct      = '04';
    case ByProduct            = '05';
    case IntermediateProduct  = '06';
    case ConsumableSupplies   = '07';
    case FixedAsset           = '08';
    case Service              = '09';
    case OtherInputs          = '10';
    case Other                = '99';

    public function label(): string
    {
        return match ($this) {
            self::MerchandiseForResale => 'Mercadoria para revenda',
            self::RawMaterial          => 'Matéria-prima',
            self::Packaging            => 'Embalagem',
            self::WorkInProgress       => 'Produto em processo',
            self::FinishedProduct      => 'Produto acabado',
            self::ByProduct            => 'Subproduto',
            self::IntermediateProduct  => 'Produto intermediário',
            self::ConsumableSupplies   => 'Material de uso e consumo',
            self::FixedAsset           => 'Ativo imobilizado',
            self::Service              => 'Serviços',
            self::OtherInputs          => 'Outros insumos',
            self::Other                => 'Outras',
        };
    }

    public function applicability(): ProductApplicabilityEnum
    {
        return match ($this) {
            self::Service                    => ProductApplicabilityEnum::Service,
            self::OtherInputs, self::Other   => ProductApplicabilityEnum::Both,
            default                          => ProductApplicabilityEnum::Product,
        };
    }

    public function controlsStock(): bool
    {
        return $this->applicability() !== ProductApplicabilityEnum::Service
            && $this !== self::FixedAsset;
    }
}