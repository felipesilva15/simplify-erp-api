<?php

namespace App\Modules\Catalog\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProductResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="type", type="string", example="Sample", minLength=1, maxLength=20),
 *     @OA\Property(property="item_type_code", type="string", example="Sample", minLength=2, maxLength=2),
 *     @OA\Property(property="sku", type="string", example="Sample", minLength=1, maxLength=40),
 *     @OA\Property(property="gtin", type="string", example="Sample", minLength=1, maxLength=14, nullable=true),
 *     @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=255),
 *     @OA\Property(property="description", type="string", example="Sample", minLength=1, maxLength=1024, nullable=true),
 *     @OA\Property(property="notes", type="string", example="Sample", minLength=1, maxLength=0, nullable=true),
 *     @OA\Property(property="price", type="number", format="float", example=20.99, minimum=0.0001, maximum=99999999999999.9999),
 *     @OA\Property(property="cost", type="number", format="float", example=20.99, minimum=0.0001, maximum=99999999999999.9999),
 *     @OA\Property(property="net_weight", type="number", format="float", example=20.99, minimum=0.001, maximum=999999999.999, nullable=true),
 *     @OA\Property(property="gross_weight", type="number", format="float", example=20.99, minimum=0.001, maximum=999999999.999, nullable=true),
 *     @OA\Property(property="width", type="number", format="float", example=20.99, minimum=0.0001, maximum=99999999.9999, nullable=true),
 *     @OA\Property(property="height", type="number", format="float", example=20.99, minimum=0.0001, maximum=99999999.9999, nullable=true),
 *     @OA\Property(property="length", type="number", format="float", example=20.99, minimum=0.0001, maximum=99999999.9999, nullable=true),
 *     @OA\Property(property="minimum_stock", type="number", format="float", example=20.99, minimum=0.0001, maximum=99999999999999.9999, nullable=true),
 *     @OA\Property(property="maximum_stock", type="number", format="float", example=20.99, minimum=0.0001, maximum=99999999999999.9999, nullable=true),
 *     @OA\Property(property="is_active", type="boolean", example=false),
 *     @OA\Property(property="measurement_unit_id", type="integer", example=1),
 *     @OA\Property(property="product_category_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="product_brand_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-10-25T23:09:57.427032Z", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-10-25T23:09:57.427032Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-10-25T23:09:57.427032Z", nullable=true)
 * )
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'item_type_code' => $this->item_type_code,
            'sku' => $this->sku,
            'gtin' => $this->gtin,
            'name' => $this->name,
            'description' => $this->description,
            'notes' => $this->notes,
            'price' => $this->price,
            'cost' => $this->cost,
            'net_weight' => $this->net_weight,
            'gross_weight' => $this->gross_weight,
            'width' => $this->width,
            'height' => $this->height,
            'length' => $this->length,
            'minimum_stock' => $this->minimum_stock,
            'maximum_stock' => $this->maximum_stock,
            'is_active' => $this->is_active,
            'measurement_unit_id' => $this->measurement_unit_id,
            'product_category_id' => $this->product_category_id,
            'product_brand_id' => $this->product_brand_id,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}