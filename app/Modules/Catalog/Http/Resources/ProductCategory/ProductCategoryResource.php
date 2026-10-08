<?php

namespace App\Modules\Catalog\Http\Resources\ProductCategory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProductCategoryResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Hidráulica", minLength=1, maxLength=120),
 *     @OA\Property(property="parent_category_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="applicability", type="string", example="PRODUCT", minLength=1, maxLength=20),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="subcategories", type="array", @OA\Items(ref="#/components/schemas/ProductCategoryResource")),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-10-07T16:16:20.103960Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-10-07T16:16:20.103960Z", nullable=true)
 * )
 */
class ProductCategoryResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent_category_id' => $this->parent_category_id,
            'subcategories' => ProductCategoryResource::collection($this->subcategories),
            'applicability' => $this->applicability,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}