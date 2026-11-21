<?php

namespace App\Modules\Catalog\Http\Resources\Service;

use App\Modules\Catalog\Http\Resources\Product\ProductMeasurementUnitResource;
use App\Modules\Catalog\Http\Resources\Product\ProductProductCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ServiceResource",
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="type", ref="#/components/schemas/ProductTypeEnum", example="SERVICE"),
 *     @OA\Property(property="item_type_code", ref="#/components/schemas/ItemTypeCodeEnum", example="09"),
 *     @OA\Property(property="sku", type="string", example="SERV-001", minLength=1, maxLength=40),
 *     @OA\Property(property="name", type="string", example="Consultoria em sistemas", minLength=1, maxLength=255),
 *     @OA\Property(property="description", type="string", example="Consultoria técnica especializada", minLength=1, maxLength=1024, nullable=true),
 *     @OA\Property(property="notes", type="string", example="Cobrança por hora", nullable=true),
 *     @OA\Property(property="price", type="string", format="decimal", example="150.00"),
 *     @OA\Property(property="cost", type="string", format="decimal", example="80.00"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="measurement_unit_id", type="integer", example=1),
 *     @OA\Property(property="measurement_unit", ref="#/components/schemas/ProductMeasurementUnitResource"),
 *     @OA\Property(property="product_category_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="category", ref="#/components/schemas/ProductProductCategoryResource"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 * )
 */
class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'item_type_code' => $this->item_type_code,
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'notes' => $this->notes,
            'price' => $this->price,
            'cost' => $this->cost,
            'is_active' => $this->is_active,
            'measurement_unit_id' => $this->measurement_unit_id,
            'measurement_unit' => $this->whenLoaded('measurementUnit', function () {
                return new ProductMeasurementUnitResource($this->measurementUnit);
            }),
            'product_category_id' => $this->product_category_id,
            'category' => $this->whenLoaded('category', function () {
                return new ProductProductCategoryResource($this->category);
            }),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
