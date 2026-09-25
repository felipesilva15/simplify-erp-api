<?php

namespace App\Modules\Partner\DTO;

use Carbon\Carbon;

class ContactDTO
{
    public function __construct(
        public int $id = 0,
        public int $partner_id = 0,
        public string $name = '',
        public string $department = '',
        public string $email = '',
        public string $mobile = '',
        public string $phone = '',
        public bool $main = false,
        public string $notes = '',
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            id: $data['id'] ?? 0,
            partner_id: $data['partner_id'] ?? 0,
            name: $data['name'] ?? '',
            department: $data['department'] ?? '',
            email: $data['email'] ?? '',
            mobile: $data['mobile'] ?? '',
            phone: $data['phone'] ?? '',
            main: $data['main'] ?? false,
            notes: $data['notes'] ?? '',
            created_at: !empty($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            updated_at: !empty($data['updated_at']) ? Carbon::parse($data['updated_at']) : null,
            deleted_at: !empty($data['deleted_at']) ? Carbon::parse($data['deleted_at']) : null
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'partner_id' => $this->partner_id,
            'name' => $this->name,
            'department' => $this->department,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'phone' => $this->phone,
            'main' => $this->main,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}