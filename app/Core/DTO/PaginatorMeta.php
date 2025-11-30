<?php

namespace App\Core\DTO;

/**
 * @OA\Schema(
 *      schema="PaginatorMeta",
 *      @OA\Property(property="per_page", type="integer", example=15),
 *      @OA\Property(property="current_page", type="integer", example=1),
 *      @OA\Property(property="last_page", type="integer", example=1),
 *      @OA\Property(property="total", type="integer", example=5),
 *      @OA\Property(property="links", type="array", @OA\Items(ref="#/components/schemas/PaginatorMetaLink"))
 * )
 */
class PaginatorMeta {
    public function __construct(
        public ?int $per_page = 0,
        public ?int $current_page = 0,
        public ?int $last_page = 0,
        public ?int $total = 0,
        public array $links = []
    ) { }

    public static function fromArray(array $data): self {
        return new self(
            per_page: $data['per_page'] ?? 0,
            current_page: $data['current_page'] ?? 0,
            last_page: $data['last_page'] ?? 0,
            total: $data['total'] ?? 0,
            links: $data['links'] ?? [],
        );
    }

    public function toArray(): array {
        return [
            'per_page' => $this->per_page,
            'current_page' => $this->current_page,
            'last_page' => $this->last_page,
            'total' => $this->total,
            'links' => $this->links
        ];
    }
}