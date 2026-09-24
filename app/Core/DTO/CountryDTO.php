<?php

namespace App\Core\DTO;

use Carbon\Carbon;

class CountryDTO
{
    public function __construct(
        public int $id = 0,
        public string $iso_code = '',
        public string $name = '',
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            id: $data['id'] ?? 0,
            iso_code: $data['iso_code'] ?? '',
            name: $data['name'] ?? '',
            created_at: !empty($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updated_at: !empty($data['updated_at']) ? Carbon::parse($data['updated_at']) : null
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'iso_code' => $this->iso_code,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}