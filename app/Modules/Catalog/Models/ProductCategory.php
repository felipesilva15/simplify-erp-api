<?php

namespace App\Modules\Catalog\Models;

use App\Core\Enums\ProductApplicabilityEnum;
use App\Core\Models\BaseModel;
use Database\Factories\ProductCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="ProductCategory",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Sample"),
 *     @OA\Property(property="parent_category_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="applicability", type="string", example="Sample"),
 *     @OA\Property(property="is_active", type="boolean", example=false),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-10-07T16:16:20.103960Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-10-07T16:16:20.103960Z", nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-10-07T16:16:20.103960Z", nullable=true)
 * )
 */
#[UseFactory(ProductCategoryFactory::class)]
class ProductCategory extends BaseModel
{
    /** @use HasFactory<\Database\Factories\ProductCategoryFactory> */
    use SoftDeletes, HasFactory;

    protected$with = [
        'subcategories'
    ];

    protected $fillable = [
        'name',
        'parent_category_id',
        'applicability',
        'is_active'
    ];

    protected $casts = [
        'applicability' => ProductApplicabilityEnum::class,
        'is_active' => 'boolean'
    ];

    public function parentCategory(): BelongsTo {
        return $this->belongsTo(ProductCategory::class, 'parent_category_id');
    }

    public function subcategories(): HasMany {
        return $this->hasMany(ProductCategory::class, 'parent_category_id');
    }
}