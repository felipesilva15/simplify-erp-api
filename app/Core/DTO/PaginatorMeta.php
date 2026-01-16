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
}