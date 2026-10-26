<?php

namespace App\Modules\Catalog\Models;

use App\Core\Models\BaseModel;
use App\Modules\Catalog\Enums\ItemTypeCodeEnum;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Product",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="type", type="string", example="Sample"),
 *     @OA\Property(property="item_type_code", type="string", example="Sample", minLength=2, maxLength=2),
 *     @OA\Property(property="sku", type="string", example="Sample"),
 *     @OA\Property(property="gtin", type="string", example="Sample", nullable=true),
 *     @OA\Property(property="name", type="string", example="Sample"),
 *     @OA\Property(property="description", type="string", example="Sample", nullable=true),
 *     @OA\Property(property="notes", type="string", example="Sample", nullable=true),
 *     @OA\Property(property="price", type="number", format="float", example=20.99),
 *     @OA\Property(property="cost", type="number", format="float", example=20.99),
 *     @OA\Property(property="net_weight", type="number", format="float", example=20.99, nullable=true),
 *     @OA\Property(property="gross_weight", type="number", format="float", example=20.99, nullable=true),
 *     @OA\Property(property="width", type="number", format="float", example=20.99, nullable=true),
 *     @OA\Property(property="height", type="number", format="float", example=20.99, nullable=true),
 *     @OA\Property(property="length", type="number", format="float", example=20.99, nullable=true),
 *     @OA\Property(property="minimum_stock", type="number", format="float", example=20.99, nullable=true),
 *     @OA\Property(property="maximum_stock", type="number", format="float", example=20.99, nullable=true),
 *     @OA\Property(property="is_active", type="boolean", example=false),
 *     @OA\Property(property="measurement_unit_id", type="integer", example=1),
 *     @OA\Property(property="product_category_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="product_brand_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-10-25T23:09:57.427032Z", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-10-25T23:09:57.427032Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-10-25T23:09:57.427032Z", nullable=true)
 * )
 */
#[UseFactory(ProductFactory::class)]
class Product extends BaseModel
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'type',
        'item_type_code',
        'sku',
        'gtin',
        'name',
        'description',
        'notes',
        'price',
        'cost',
        'net_weight',
        'gross_weight',
        'width',
        'height',
        'length',
        'minimum_stock',
        'maximum_stock',
        'is_active',
        'measurement_unit_id',
        'product_category_id',
        'product_brand_id'
    ];

    protected $casts = [
        'item_type_code' => ItemTypeCodeEnum::class,
        'price' => 'decimal:4',
        'cost' => 'decimal:4',
        'net_weight' => 'decimal:3',
        'gross_weight' => 'decimal:3',
        'width' => 'decimal:4',
        'height' => 'decimal:4',
        'length' => 'decimal:4',
        'minimum_stock' => 'decimal:4',
        'maximum_stock' => 'decimal:4',
        'is_active' => 'boolean'
    ];

    public function measurementUnit(): BelongsTo {
        return $this->belongsTo(MeasurementUnit::class);
    }

    public function category(): BelongsTo {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function brand(): BelongsTo {
        return $this->belongsTo(ProductBrand::class, 'product_brand_id');
    }
}