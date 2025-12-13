<?php

namespace App\Modules\Auth\DTO;

use Carbon\Carbon;

class PermissionDTO
{
    public function __construct(
        public int $id = 0,
        public int $module_id = 0,
        public string $group = '',
        public string $action = '',
        public ?string $description = '',
        public ?bool $has_access_free = false,
        public ?bool $is_active = false,
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            id: $data['id'] ?? 0,
            module_id: $data['module_id'] ?? 0,
            group: $data['group'] ?? '',
            action: $data['action'] ?? '',
            description: $data['description'] ?? '',
            has_access_free: $data['has_access_free'] ?? false,
            is_active: $data['is_active'] ?? false,
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            deleted_at: $data['deleted_at'] ?? null
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'module_id' => $this->module_id,
            'group' => $this->group,
            'action' => $this->action,
            'description' => $this->description,
            'has_access_free' => $this->has_access_free,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}