<?php

namespace App\Core\OA\Schemas;

/**
 * @OA\Schema(
 *     schema="FieldFilter",
 *     type="object",
 *     additionalProperties=false,
 *     @OA\Property(property="eq",  ref="#/components/schemas/FilterValue"),
 *     @OA\Property(property="ne", ref="#/components/schemas/FilterValue"),
 *     @OA\Property(property="gt",  ref="#/components/schemas/FilterValue"),
 *     @OA\Property(property="gte", ref="#/components/schemas/FilterValue"),
 *     @OA\Property(property="lt",  ref="#/components/schemas/FilterValue"),
 *     @OA\Property(property="lte", ref="#/components/schemas/FilterValue"),
 *     @OA\Property(property="like",ref="#/components/schemas/FilterValue")
 * )
 */
class FieldFilter {

}