<?php

namespace App\Core\Http\Resources\City;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CityResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=90),
 *     @OA\Property(property="ibge_code", type="string", example="Sample", minLength=1, maxLength=7),
 *     @OA\Property(property="state", ref="#/components/schemas/CityStateResource"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-09-24T17:20:04.068390Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-09-24T17:20:04.068390Z", nullable=true)
 * )
 */
class CityResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ibge_code' => $this->ibge_code,
            'state' => CityStateResource::make($this->state),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}