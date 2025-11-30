<?php

namespace App\Core\DTO;

/**
 * @OA\Schema(
 *      schema="PaginatorMetaLink",
 *      @OA\Property(property="url", type="string", example="http://localhost:8000/api/data?page=1"),
 *      @OA\Property(property="page", type="integer", example=1),
 *      @OA\Property(property="active", type="boolean", example=true)
 * )
 */
class PaginatorMetaLink {
    public function __construct(
        public ?string $url = '',
        public ?int $page = 0,
        public ?bool $active = false,
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            url: $data['url'] ?? '',
            page: $data['page'] ?? 0,
            active: $data['active'] ?? false
        );
    }

    public function toArray(): array {
        return [
            'url' => $this->url,
            'page' => $this->page,
            'active' => $this->active
        ];
    }
}