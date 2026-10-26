<?php

namespace App\Modules\Catalog\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="ProductLookupResource",
 *      @OA\Property(property="key", type="integer", example=1),
 *      @OA\Property(property="label", type="string", example="Sample", minLength=1, maxLength=255),
 *      @OA\Property(property="sublabel", type="string", example="Cod.: 1", minLength=1, maxLength=80),
 *      @OA\Property(
 *          property="meta",
 *          type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=255),
 *         @OA\Property(property="type", type="string", example="Sample", minLength=1, maxLength=20),
 *         @OA\Property(property="item_type_code", type="string", example="Sample", minLength=2, maxLength=2),
 *         @OA\Property(property="sku", type="string", example="Sample", minLength=1, maxLength=40),
 *         @OA\Property(property="gtin", type="string", example="Sample", minLength=1, maxLength=14, nullable=true)
 *      )
 * )
 */
class ProductLookupResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'key' => $this->id,
            'label' => $this->name,
            'sublabel' => "Cód.: {$this->id}",
            'meta' => $this->only('id', 'name', 'type', 'item_type_code', 'sku', 'gtin')
        ];
    }
}