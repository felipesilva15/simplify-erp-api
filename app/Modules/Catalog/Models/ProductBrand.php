<?php

namespace App\Modules\Catalog\Models;

use App\Core\Models\BaseModel;
use Database\Factories\ProductBrandFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="ProductBrand",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Hauwei"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-10-23T00:34:36.763327Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-10-23T00:34:36.763327Z", nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-10-23T00:34:36.763327Z", nullable=true)
 * )
 */
#[UseFactory(ProductBrandFactory::class)]
class ProductBrand extends BaseModel
{
    /** @use HasFactory<\Database\Factories\ProductBrandFactory> */
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name'
    ];

    protected $casts = [
        
    ];
}