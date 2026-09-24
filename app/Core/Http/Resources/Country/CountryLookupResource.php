<?php

namespace App\Core\Http\Resources\Country;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="CountryLookupResource",
 *      @OA\Property(property="key", type="integer", example=1),
 *      @OA\Property(property="label", type="string", example="Sample", minLength=1, maxLength=60),
 *      @OA\Property(property="sublabel", type="string", example="Cod.: 1", minLength=1, maxLength=80),
 *      @OA\Property(
 *          property="meta",
 *          type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=60),
 *         @OA\Property(property="iso_code", type="string", example="Sample", minLength=1, maxLength=2)
 *      )
 * )
 */
class CountryLookupResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'key' => $this->id,
            'label' => $this->name,
            'sublabel' => "Cod.: {$this->id} | ISO: {$this->iso_code}",
            'meta' => $this->only('id', 'name', 'iso_code')
        ];
    }
}