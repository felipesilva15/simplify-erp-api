<?php

namespace App\Core\DTO;

use Carbon\Carbon;

class StateDTO
{
    public function __construct(
        public int $id = 0,
        public int $country_id = 0,
        public string $name = '',
        public string $uf = '',
        public string $ibge_code = '',
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            id: $data['id'] ?? 0,
            country_id: $data['country_id'] ?? 0,
            name: $data['name'] ?? '',
            uf: $data['uf'] ?? '',
            ibge_code: $data['ibge_code'] ?? '',
            created_at: !empty($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updated_at: !empty($data['updated_at']) ? Carbon::parse($data['updated_at']) : null
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'country_id' => $this->country_id,
            'name' => $this->name,
            'uf' => $this->uf,
            'ibge_code' => $this->ibge_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}