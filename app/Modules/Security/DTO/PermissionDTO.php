<?php

namespace App\Modules\Security\DTO;

use Carbon\Carbon;

class PermissionDTO
{
    public function __construct(
        public int $id = 0,
        public int $module_id = 0,
        public string $resource = '',
        public string $action = '',
        public string $name = '',
        public ?string $description = '',
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            id: $data['id'] ?? 0,
            module_id: $data['module_id'] ?? 0,
            resource: $data['resource'] ?? '',
            action: $data['action'] ?? '',
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            deleted_at: $data['deleted_at'] ?? null
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'module_id' => $this->module_id,
            'resource' => $this->resource,
            'action' => $this->action,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}