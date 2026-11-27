<?php

namespace App\Modules\Finance\Http\Resources\FinanceCategory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="FinanceCategoryResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="parent_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="code", type="string", example="1", minLength=1, maxLength=20),
 *     @OA\Property(property="name", type="string", example="Materiais", minLength=1, maxLength=100),
 *     @OA\Property(property="applicable_type", ref="#/components/schemas/FinanceApplicabilityEnum"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-11-27T22:31:29.964450Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-11-27T22:31:29.964450Z", nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-11-27T22:31:29.964450Z", nullable=true)
 * )
 */
class FinanceCategoryResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'code' => $this->code,
            'name' => $this->name,
            'applicable_type' => $this->applicable_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}