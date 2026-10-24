<?php

namespace App\Modules\Catalog\DTO;

use App\Modules\Catalog\Enums\MeasurementUnitCategoryEnum;
use Carbon\Carbon;

class MeasurementUnitDTO
{
    public function __construct(
        public int $id = 0,
        public string $code = '',
        public string $name = '',
        public string $symbol = '',
        public string|MeasurementUnitCategoryEnum $category = '',
        public ?int $base_unit_id = null,
        public float|string $conversion_factor = 1.0,
        public int $decimal_places = 0,
        public ?string $external_code = null,
        public bool $is_system = false,
        public bool $is_active = true,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            id: (int) ($data['id'] ?? 0),
            code: (string) ($data['code'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            symbol: (string) ($data['symbol'] ?? ''),
            category: $data['category'] ?? '',
            base_unit_id: isset($data['base_unit_id']) && $data['base_unit_id'] !== '' ? (int) $data['base_unit_id'] : null,
            conversion_factor: $data['conversion_factor'] ?? 1.0,
            decimal_places: isset($data['decimal_places']) ? (int) $data['decimal_places'] : 0,
            external_code: !empty($data['external_code']) ? (string) $data['external_code'] : null,
            is_system: isset($data['is_system']) ? (bool) $data['is_system'] : false,
            is_active: isset($data['is_active']) ? (bool) $data['is_active'] : true,
            created_at: !empty($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updated_at: !empty($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
            deleted_at: !empty($data['deleted_at']) ? Carbon::parse($data['deleted_at']) : null
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'category' => $this->category instanceof MeasurementUnitCategoryEnum ? $this->category->value : $this->category,
            'base_unit_id' => $this->base_unit_id,
            'conversion_factor' => $this->conversion_factor,
            'decimal_places' => $this->decimal_places,
            'external_code' => $this->external_code,
            'is_system' => $this->is_system,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
