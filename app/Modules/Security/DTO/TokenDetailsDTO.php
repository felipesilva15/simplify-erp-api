<?php

namespace App\Modules\Security\DTO;

class TokenDetailsDTO
{
    public function __construct(
        public string $token = '',
        public string $type = '',
        public int $expiresIn = 0
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            token: $data['token'] ?? '',
            type: $data['type'] ?? '',
            expiresIn: $data['expiresIn'] ?? 0
        );
    }

    public function toArray(): array {
        return [
            'token' => $this->token,
            'type' => $this->type,
            'expiresIn' => $this->expiresIn
        ];
    }
}