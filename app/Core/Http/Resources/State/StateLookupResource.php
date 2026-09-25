<?php

namespace App\Core\Http\Resources\State;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="StateLookupResource",
 *      @OA\Property(property="key", type="integer", example=1),
 *      @OA\Property(property="label", type="string", example="Sample", minLength=1, maxLength=80),
 *      @OA\Property(property="sublabel", type="string", example="Cod.: 1", minLength=1, maxLength=80),
 *      @OA\Property(
 *          property="meta",
 *          type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=80),
 *         @OA\Property(property="uf", type="string", example="Sample", minLength=1, maxLength=2),
 *         @OA\Property(property="ibge_code", type="string", example="Sample", minLength=1, maxLength=2)
 *      )
 * )
 */
class StateLookupResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'key' => $this->id,
            'label' => $this->name,
            'sublabel' => "Cod.: {$this->id} | UF: {$this->uf} | Cod. IBGE: {$this->ibge_code}",
            'meta' => $this->only('id', 'name', 'uf', 'ibge_code')
        ];
    }
}