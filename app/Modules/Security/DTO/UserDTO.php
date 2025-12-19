<?php

namespace App\Modules\Security\DTO;

use Carbon\Carbon;

class UserDTO
{
    public array $fieldsToUse = [];

    public function __construct(
        public int $id = 0,
        public string $name = '',
        public string $email = '',
        public ?Carbon $email_verified_at = null,
        public string $password = '',
        public ?string $remember_token = '',
        public ?string $username = '',
        public ?string $phone_number = '',
        public bool $is_admin = false,
        public array $roles = [],
        public ?Carbon $created_at = null,
        public ?Carbon $updated_at = null,
        public ?Carbon $deleted_at = null
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            id: $data['id'] ?? 0,
            name: $data['name'] ?? '',
            email: $data['email'] ?? '',
            email_verified_at: $data['email_verified_at'] ?? null,
            password: $data['password'] ?? '',
            remember_token: $data['remember_token'] ?? '',
            username: $data['username'] ?? '',
            phone_number: $data['phone_number'] ?? '',
            is_admin: $data['is_admin'] ?? false,
            roles: $data['roles'] ?? [],
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            deleted_at: $data['deleted_at'] ?? null
        );
    }

    public function toArray(): array {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'password' => $this->password,
            'remember_token' => $this->remember_token,
            'username' => $this->username,
            'phone_number' => $this->phone_number,
            'is_admin' => $this->is_admin,
            'roles' => $this->roles,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];

        return array_filter(
            $data,
            fn ($value, $key) => empty($this->fieldsToUse) || in_array($key, $this->fieldsToUse),
            ARRAY_FILTER_USE_BOTH
        );
    }
}