<?php

namespace App\Core\Http\Resources\Country;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CountryResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="iso_code", type="string", example="Sample", minLength=1, maxLength=2),
 *     @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=60),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-09-24T03:46:27.255709Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-09-24T03:46:27.255709Z", nullable=true)
 * )
 */
class CountryResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'iso_code' => $this->iso_code,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}