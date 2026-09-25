<?php

namespace App\Core\DTO;

use Carbon\Carbon;

class CityDTO
{
    public function __construct(
        public int $id = 0,
        public int $state_id = 0,
        public string $name = '',
        public string $ibge_code = '',
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            id: $data['id'] ?? 0,
            state_id: $data['state_id'] ?? 0,
            name: $data['name'] ?? '',
            ibge_code: $data['ibge_code'] ?? '',
            created_at: !empty($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updated_at: !empty($data['updated_at']) ? Carbon::parse($data['updated_at']) : null
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'state_id' => $this->state_id,
            'name' => $this->name,
            'ibge_code' => $this->ibge_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}