<?php

namespace App\Modules\Catalog\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProductMeasurementUnitResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="code", type="string", example="UN", minLength=1, maxLength=10),
 *     @OA\Property(property="name", type="string", example="Unidade", minLength=1, maxLength=60),
 *     @OA\Property(property="symbol", type="string", example="un", minLength=1, maxLength=10),
 * )
 */
class ProductMeasurementUnitResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'symbol' => $this->symbol
        ];
    }
}