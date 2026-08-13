<?php

namespace App\Core\DTO;

use App\Core\Enums\ActivityActionEnum;
use Carbon\Carbon;

class ActivityLogDTO
{
    public function __construct(
        public int $id = 0,
        public string $origin_type = '',
        public string $origin_id = '',
        public ?ActivityActionEnum $action = null,
        public ?int $user_id = 0,
        public ?string $description = '',
        public ?string $route_name = '',
        public ?string $route_path = '',
        public ?string $ip_address = '',
        public ?string $user_agent = '',
        public ?Carbon $created_at = null,
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            id: $data['id'] ?? 0,
            origin_type: $data['origin_type'] ?? '',
            origin_id: $data['origin_id'] ?? '',
            action: $data['action'] ?? null,
            user_id: $data['user_id'] ?? 0,
            description: $data['description'] ?? '',
            route_name: $data['route_name'] ?? '',
            route_path: $data['route_path'] ?? '',
            ip_address: $data['ip_address'] ?? '',
            user_agent: $data['user_agent'] ?? '',
            created_at: $data['created_at'] ?? null,
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'origin_type' => $this->origin_type,
            'origin_id' => $this->origin_id,
            'action' => $this->action,
            'user_id' => $this->user_id,
            'description' => $this->description,
            'route_name' => $this->route_name,
            'route_path' => $this->route_path,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'created_at' => $this->created_at,
        ];
    }
}