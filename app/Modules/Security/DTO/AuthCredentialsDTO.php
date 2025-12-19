<?php

namespace App\Modules\Security\DTO;

class AuthCredentialsDTO
{
    public function __construct(
        public string $username = '',
        public string $password = ''
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            username: $data['username'] ?? '',
            password: $data['password'] ?? ''
        );
    }

    public function toArray(): array {
        return [
            'username' => $this->username,
            'password' => $this->password
        ];
    }
}