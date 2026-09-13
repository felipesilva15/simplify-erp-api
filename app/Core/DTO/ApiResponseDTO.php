<?php

namespace App\Core\DTO;

use App\Core\Helpers\ListHelpers;

/**
 * @OA\Schema(
 *   schema="ApiResponse",
 *   type="object",
 *   required={"success","message","data"},
 *   @OA\Property(property="success", type="boolean", example=true),
 *   @OA\Property(property="message", type="string", example="Operação realizada com sucesso"),
 *   @OA\Property(property="data", nullable=true),
 *   @OA\Property(property="warnings", type="array", @OA\Items(type="string"), nullable=true),
 *   @OA\Property(property="links", type="object", nullable=true),
 *   @OA\Property(property="meta", type="object", nullable=true)
 * )
 */
class ApiResponseDTO {
    public function __construct(
        public bool $success,
        public string $message,
        public mixed $data,
        public mixed $links,
        public ?array $warnings,
        public mixed $errors,
        public mixed $meta
    ) { }

    public function toArray(): array {
        $apiResponse = [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'warnings' => $this->warnings,
            'links' => $this->links ?: null,
            'errors' => $this->errors,
            'meta' => $this->meta
        ];

        return ListHelpers::removeNullProperties($apiResponse);
    }
}