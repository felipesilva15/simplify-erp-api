<?php

namespace App\Modules\Catalog\DTO;

use Carbon\Carbon;

class ProductCategoryDTO
{
    public function __construct(
        public int $id = 0,
        public string $name = '',
        public ?int $parent_category_id = null,
        public string $applicability = '',
        public bool $is_active = false,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            id: $data['id'] ?? 0,
            name: $data['name'] ?? '',
            parent_category_id: $data['parent_category_id'] ?? null,
            applicability: $data['applicability'] ?? '',
            is_active: $data['is_active'] ?? false,
            created_at: !empty($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updated_at: !empty($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
            deleted_at: !empty($data['deleted_at']) ? Carbon::parse($data['deleted_at']) : null
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent_category_id' => $this->parent_category_id,
            'applicability' => $this->applicability,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}