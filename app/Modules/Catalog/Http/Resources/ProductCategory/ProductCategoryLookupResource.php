<?php

namespace App\Modules\Catalog\Http\Resources\ProductCategory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="ProductCategoryLookupResource",
 *      @OA\Property(property="key", type="integer", example=1),
 *      @OA\Property(property="label", type="string", example="Hidráulica", minLength=1, maxLength=120),
 *      @OA\Property(property="sublabel", type="string", example="Cod.: 1", minLength=1, maxLength=80),
 *      @OA\Property(
 *          property="meta",
 *          type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Hidráulica", minLength=1, maxLength=120),
 *         @OA\Property(property="applicability", type="string", example="PRODUCT", minLength=1, maxLength=20)
 *      )
 * )
 */
class ProductCategoryLookupResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'key' => $this->id,
            'label' => $this->name,
            'sublabel' => "Cód.: {$this->id}",
            'meta' => $this->only('id', 'name', 'applicability')
        ];
    }
}