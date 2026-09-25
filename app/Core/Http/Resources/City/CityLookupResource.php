<?php

namespace App\Core\Http\Resources\City;

use App\Core\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="CityLookupResource",
 *      @OA\Property(property="key", type="integer", example=1),
 *      @OA\Property(property="label", type="string", example="Sample", minLength=1, maxLength=90),
 *      @OA\Property(property="sublabel", type="string", example="Cod.: 1", minLength=1, maxLength=80),
 *      @OA\Property(
 *          property="meta",
 *          type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=90),
 *         @OA\Property(property="ibge_code", type="string", example="Sample", minLength=1, maxLength=7)
 *      )
 * )
 */
class CityLookupResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'key' => $this->id,
            'label' => $this->name,
            'sublabel' => "Cód.: {$this->id} | UF: {$this->state->uf} | Cód. IBGE: {$this->ibge_code}",
            'meta' => $this->only('id', 'name', 'ibge_code')
        ];
    }
}