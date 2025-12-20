<?php

namespace App\Core\DTO;

/**
 * @OA\Schema(
 *   schema="ApiResponse",
 *   type="object",
 *   required={"success","message","data","errors","meta"},
 *   @OA\Property(property="success", type="boolean", example=true),
 *   @OA\Property(property="message", type="string", example="Operação realizada com sucesso"),
 *   @OA\Property(property="links", type="object", nullable=true),
 *   @OA\Property(property="warnings", type="array", @OA\Items(type="string", example="Este recurso não pode ser editado."), nullable=true),
 *   @OA\Property(property="errors", type="object", nullable=true),
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
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'warnings' => $this->warnings,
            'links' => $this->links ?: null,
            'errors' => $this->errors,
            'meta' => $this->meta
        ];
    }
}