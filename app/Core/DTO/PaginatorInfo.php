<?php

namespace App\Core\DTO;

/**
 * @OA\Schema(
 *      schema="PaginatorInfo",
 *      @OA\Property(property="links", ref="#/components/schemas/PaginatorLinks"),
 *      @OA\Property(property="meta", ref="#/components/schemas/PaginatorMeta")
 * )
 */
class PaginatorInfo {
    public function __construct(
        public ?PaginatorLinks $links = null,
        public ?PaginatorMeta $meta = null
    ) { }

    public function toArray(): array {
        return [
            'links' => $this->links,
            'meta' => $this->meta
        ];
    }
}