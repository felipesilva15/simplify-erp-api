<?php

namespace App\Modules\Catalog\DTO;

use Carbon\Carbon;

class ProductDTO
{
    public function __construct(
        public int $id = 0,
        public string $type = '',
        public string $item_type_code = '',
        public string $sku = '',
        public ?string $gtin = '',
        public string $name = '',
        public ?string $description = '',
        public ?string $notes = '',
        public float $price = 0,
        public float $cost = 0,
        public ?float $net_weight = 0,
        public ?float $gross_weight = 0,
        public ?float $width = 0,
        public ?float $height = 0,
        public ?float $length = 0,
        public ?float $minimum_stock = 0,
        public ?float $maximum_stock = 0,
        public bool $is_active = false,
        public int $measurement_unit_id = 0,
        public ?int $product_category_id = 0,
        public ?int $product_brand_id = 0,
        public ?Carbon $deleted_at = null,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            id: $data['id'] ?? 0,
            type: $data['type'] ?? '',
            item_type_code: $data['item_type_code'] ?? '',
            sku: $data['sku'] ?? '',
            gtin: $data['gtin'] ?? '',
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            notes: $data['notes'] ?? '',
            price: $data['price'] ?? 0,
            cost: $data['cost'] ?? 0,
            net_weight: $data['net_weight'] ?? null,
            gross_weight: $data['gross_weight'] ?? null,
            width: $data['width'] ?? null,
            height: $data['height'] ?? null,
            length: $data['length'] ?? null,
            minimum_stock: $data['minimum_stock'] ?? null,
            maximum_stock: $data['maximum_stock'] ?? null,
            is_active: $data['is_active'] ?? false,
            measurement_unit_id: $data['measurement_unit_id'] ?? 0,
            product_category_id: $data['product_category_id'] ?? null,
            product_brand_id: $data['product_brand_id'] ?? null,
            deleted_at: !empty($data['deleted_at']) ? Carbon::parse($data['deleted_at']) : null,
            created_at: !empty($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updated_at: !empty($data['updated_at']) ? Carbon::parse($data['updated_at']) : null
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'item_type_code' => $this->item_type_code,
            'sku' => $this->sku,
            'gtin' => $this->gtin,
            'name' => $this->name,
            'description' => $this->description,
            'notes' => $this->notes,
            'price' => $this->price,
            'cost' => $this->cost,
            'net_weight' => $this->net_weight,
            'gross_weight' => $this->gross_weight,
            'width' => $this->width,
            'height' => $this->height,
            'length' => $this->length,
            'minimum_stock' => $this->minimum_stock,
            'maximum_stock' => $this->maximum_stock,
            'is_active' => $this->is_active,
            'measurement_unit_id' => $this->measurement_unit_id,
            'product_category_id' => $this->product_category_id,
            'product_brand_id' => $this->product_brand_id,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}