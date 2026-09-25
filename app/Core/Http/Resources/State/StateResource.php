<?php

namespace App\Core\Http\Resources\State;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="StateResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="country_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=80),
 *     @OA\Property(property="uf", type="string", example="Sample", minLength=1, maxLength=2),
 *     @OA\Property(property="ibge_code", type="string", example="Sample", minLength=1, maxLength=2),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-09-24T04:09:20.641277Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-09-24T04:09:20.641277Z", nullable=true)
 * )
 */
class StateResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'country_id' => $this->country_id,
            'name' => $this->name,
            'uf' => $this->uf,
            'ibge_code' => $this->ibge_code,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}