<?php

namespace App\Modules\Catalog\Http\Resources\MeasurementUnit;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="MeasurementUnitResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="code", type="string", example="KG", minLength=1, maxLength=10),
 *     @OA\Property(property="name", type="string", example="Quilograma", minLength=1, maxLength=60),
 *     @OA\Property(property="symbol", type="string", example="kg", minLength=1, maxLength=10),
 *     @OA\Property(property="category", type="string", example="MASS", minLength=1, maxLength=20),
 *     @OA\Property(property="base_unit_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="conversion_factor", type="number", format="float", example=1.0, minimum=0.00000001, maximum=9999999999.99999999),
 *     @OA\Property(property="decimal_places", type="integer", example=3),
 *     @OA\Property(property="external_code", type="string", example="KGM", minLength=1, maxLength=10, nullable=true),
 *     @OA\Property(property="is_system", type="boolean", example=false),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-10-23T17:17:05.207961Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-10-23T17:17:05.207961Z", nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-10-23T17:17:05.207961Z", nullable=true)
 * )
 */
class MeasurementUnitResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'category' => $this->category,
            'base_unit_id' => $this->base_unit_id,
            'conversion_factor' => $this->conversion_factor,
            'decimal_places' => $this->decimal_places,
            'external_code' => $this->external_code,
            'is_system' => $this->is_system,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
