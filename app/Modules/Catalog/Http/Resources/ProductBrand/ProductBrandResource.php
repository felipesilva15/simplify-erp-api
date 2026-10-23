<?php

namespace App\Modules\Catalog\Http\Resources\ProductBrand;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProductBrandResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Hauwei", minLength=1, maxLength=120),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-10-23T00:34:36.763327Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-10-23T00:34:36.763327Z", nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-10-23T00:34:36.763327Z", nullable=true)
 * )
 */
class ProductBrandResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}