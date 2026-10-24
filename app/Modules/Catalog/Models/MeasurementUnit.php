<?php

namespace App\Modules\Catalog\Models;

use App\Core\Models\BaseModel;
use App\Modules\Catalog\Enums\MeasurementUnitCategoryEnum;
use Database\Factories\MeasurementUnitFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="MeasurementUnit",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="code", type="string", example="KG"),
 *     @OA\Property(property="name", type="string", example="Quilograma"),
 *     @OA\Property(property="symbol", type="string", example="kg"),
 *     @OA\Property(property="category", type="string", example="MASS"),
 *     @OA\Property(property="base_unit_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="conversion_factor", type="number", format="float", example=1.0),
 *     @OA\Property(property="decimal_places", type="integer", example=3),
 *     @OA\Property(property="external_code", type="string", example="KGM", nullable=true),
 *     @OA\Property(property="is_system", type="boolean", example=false),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-10-23T17:17:05.207961Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-10-23T17:17:05.207961Z", nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-10-23T17:17:05.207961Z", nullable=true)
 * )
 */
#[UseFactory(MeasurementUnitFactory::class)]
class MeasurementUnit extends BaseModel
{
    /** @use HasFactory<\Database\Factories\MeasurementUnitFactory> */
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'category',
        'base_unit_id',
        'conversion_factor',
        'decimal_places',
        'external_code',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'category' => MeasurementUnitCategoryEnum::class,
        'conversion_factor' => 'decimal:8',
        'decimal_places' => 'integer',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(MeasurementUnit::class, 'base_unit_id');
    }

    public function derivedUnits(): HasMany
    {
        return $this->hasMany(MeasurementUnit::class, 'base_unit_id');
    }
}
