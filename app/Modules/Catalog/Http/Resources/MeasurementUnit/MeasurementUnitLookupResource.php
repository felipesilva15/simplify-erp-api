<?php

namespace App\Modules\Catalog\Http\Resources\MeasurementUnit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="MeasurementUnitLookupResource",
 *      @OA\Property(property="key", type="string", example="Sample"),
 *      @OA\Property(property="label", type="string", example="Sample", minLength=1, maxLength=60),
 *      @OA\Property(property="sublabel", type="string", example="Cod.: 1", minLength=1, maxLength=80),
 *      @OA\Property(
 *          property="meta",
 *          type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=60),
 *         @OA\Property(property="code", type="string", example="Sample", minLength=1, maxLength=10),
 *         @OA\Property(property="symbol", type="string", example="Sample", minLength=1, maxLength=10),
 *         @OA\Property(property="category", type="string", example="Sample", minLength=1, maxLength=20)
 *      )
 * )
 */
class MeasurementUnitLookupResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'key' => $this->id,
            'label' => $this->name,
            'sublabel' => "Cód: {$this->id} | Símbolo: {$this->symbol}",
            'meta' => $this->only('id', 'name', 'code', 'symbol', 'category')
        ];
    }
}