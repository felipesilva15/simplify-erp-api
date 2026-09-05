<?php

namespace App\Modules\Partner\Http\Resources\PartnerType;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="PartnerTypeResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=60),
 *      @OA\Property(property="code", type="string", example="Sample", minLength=1, maxLength=3),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2026-09-05T06:33:15.515561Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2026-09-05T06:33:15.515561Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-09-05T06:33:15.515561Z", nullable=true)
 * )
 */
class PartnerTypeResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}