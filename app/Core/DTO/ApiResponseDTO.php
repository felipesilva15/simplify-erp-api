<?php

namespace App\Core\DTO;

use App\Core\Helpers\ListHelpers;

/**
 * @OA\Schema(
 *   schema="ApiResponse",
 *   type="object",
 *   required={"success","message","data"},
 *
 *   @OA\Property(property="success", type="boolean", example=true),
 *   @OA\Property(property="message", type="string", example="Operação realizada com sucesso"),
 *   @OA\Property(property="data", nullable=true, example={"id": 1}),
 *   @OA\Property(property="warnings", type="array", @OA\Items(type="string"), nullable=true, example={"No records found."}),
 *   @OA\Property(property="links", type="object", nullable=true, example={"first":"http://localhost:8000/api/catalog/products?page=1","previous":null,"next":null,"last":"http://localhost:8000/api/catalog/products?page=1"}),
 *   @OA\Property(property="meta", type="object", nullable=true, example={"per_page":15,"current_page":1,"last_page":1,"total":1}),
 *   @OA\Property(property="errors", type="object", nullable=true, example=null)
 * )
 */
class ApiResponseDTO
{
    public function __construct(
        public bool $success,
        public string $message,
        public mixed $data,
        public mixed $links,
        public ?array $warnings,
        public mixed $errors,
        public mixed $meta
    ) {}

    public function toArray(): array
    {
        $apiResponse = [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'warnings' => $this->warnings,
            'links' => $this->links ?: null,
            'errors' => $this->errors,
            'meta' => $this->meta,
        ];

        return ListHelpers::removeNullProperties($apiResponse);
    }
}
