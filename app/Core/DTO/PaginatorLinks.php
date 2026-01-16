<?php

namespace App\Core\DTO;

/**
 * @OA\Schema(
 *      schema="PaginatorLinks",
 *      @OA\Property(property="first", type="string", example="http://localhost:8000/api/data?page=1"),
 *      @OA\Property(property="previous", type="string", example="http://localhost:8000/api/data?page=1"),
 *      @OA\Property(property="next", type="string", example="http://localhost:8000/api/data?page=3"),
 *      @OA\Property(property="last", type="string", example="http://localhost:8000/api/data?page=3")
 * )
 */
class PaginatorLinks {
    public function __construct(
        public ?string $first = '',
        public ?string $previous = '',
        public ?string $next = '',
        public ?string $last = '',
    ) { }
}