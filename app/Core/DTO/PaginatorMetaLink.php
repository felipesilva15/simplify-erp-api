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
}