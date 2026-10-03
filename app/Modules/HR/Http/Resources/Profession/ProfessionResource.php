<?php

namespace App\Modules\HR\Http\Resources\Profession;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProfessionResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="cbo", type="string", example="Sample", minLength=1, maxLength=6),
 *     @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=180),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-10-01T12:41:58.875427Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-10-01T12:41:58.875427Z", nullable=true)
 * )
 */
class ProfessionResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'cbo' => $this->cbo,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}