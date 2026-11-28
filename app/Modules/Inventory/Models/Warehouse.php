<?php

namespace App\Modules\Inventory\Models;

use App\Core\Models\BaseModel;
use Database\Factories\WarehouseFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Warehouse",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Almoxarifado"),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-11-28T01:54:40.911680Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-11-28T01:54:40.911680Z", nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-11-28T01:54:40.911680Z", nullable=true)
 * )
 */
#[UseFactory(WarehouseFactory::class)]
class Warehouse extends BaseModel
{
    /** @use HasFactory<\Database\Factories\WarehouseFactory> */
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}