<?php

namespace App\Core\DTO;

use Carbon\Carbon;

class ResourceDTO
{
    public function __construct(
        public int $id = 0,
        public string $name = '',
        public string $slug = '',
        public ?string $description = '',
        public ?int $module_id = 0,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            id: $data['id'] ?? 0,
            name: $data['name'] ?? '',
            slug: $data['slug'] ?? '',
            description: $data['description'] ?? '',
            module_id: $data['module_id'] ?? 0,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            deleted_at: $data['deleted_at'] ?? null
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'module_id' => $this->module_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}