<?php

namespace App\Modules\Finance\DTO;

use Carbon\Carbon;

class FinanceCategoryDTO
{
    public function __construct(
        public int $id = 0,
        public ?int $parent_id = null,
        public string $code = '',
        public string $name = '',
        public string $applicable_type = '',
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            id: $data['id'] ?? 0,
            parent_id: $data['parent_id'] ?? null,
            code: $data['code'] ?? '',
            name: $data['name'] ?? '',
            applicable_type: $data['applicable_type'] ?? '',
            created_at: !empty($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updated_at: !empty($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
            deleted_at: !empty($data['deleted_at']) ? Carbon::parse($data['deleted_at']) : null
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'code' => $this->code,
            'name' => $this->name,
            'applicable_type' => $this->applicable_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}