<?php

namespace App\Modules\Construction\Http\Resources\ProjectType;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProjectTypeResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Reforma", minLength=1, maxLength=80),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-11-21T23:11:04.640320Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-11-21T23:11:04.640320Z", nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-11-21T23:11:04.640320Z", nullable=true)
 * )
 */
class ProjectTypeResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}