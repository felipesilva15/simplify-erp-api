<?php

namespace App\Modules\Geography\DTO;

use Carbon\Carbon;

class AddressDTO
{
    public function __construct(
        public int $id = 0,
        public string $addressable_type = '',
        public int $addressable_id = 0,
        public string $type = '',
        public bool $is_primary = false,
        public int $country_id = 0,
        public ?int $state_id = null,
        public ?int $city_id = null,
        public ?string $district = '',
        public ?string $street = '',
        public ?string $number = '',
        public ?string $state_name = '',
        public ?string $city_name = '',
        public ?string $address_line_1 = '',
        public ?string $address_line_2 = '',
        public ?string $postal_code = '',
        public ?string $complement = '',
        public ?string $notes = '',
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            addressable_type: $data['addressable_type'] ?? '',
            addressable_id: $data['addressable_id'] ?? 0,
            type: $data['type'] ?? '',
            is_primary: $data['is_primary'] ?? false,
            country_id: $data['country_id'] ?? 0,
            state_id: $data['state_id'] ?? null,
            city_id: $data['city_id'] ?? null,
            district: $data['district'] ?? '',
            street: $data['street'] ?? '',
            number: $data['number'] ?? '',
            state_name: $data['state_name'] ?? '',
            city_name: $data['city_name'] ?? '',
            address_line_1: $data['address_line_1'] ?? '',
            address_line_2: $data['address_line_2'] ?? '',
            postal_code: $data['postal_code'] ?? '',
            complement: $data['complement'] ?? '',
            notes: $data['notes'] ?? '',
            created_at: ! empty($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updated_at: ! empty($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
            deleted_at: ! empty($data['deleted_at']) ? Carbon::parse($data['deleted_at']) : null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'addressable_type' => $this->addressable_type,
            'addressable_id' => $this->addressable_id,
            'type' => $this->type,
            'is_primary' => $this->is_primary,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' => $this->city_id,
            'district' => $this->district,
            'street' => $this->street,
            'number' => $this->number,
            'state_name' => $this->state_name,
            'city_name' => $this->city_name,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'postal_code' => $this->postal_code,
            'complement' => $this->complement,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
