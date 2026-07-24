<?php

namespace App\Core\Models;

use App\Modules\Security\Models\Permission;
use Database\Factories\ModuleFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *      schema="Module",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample"),
 *      @OA\Property(property="description", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="is_active", type="boolean", example=false, nullable=true),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-05T00:30:46.131084Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-05T00:30:46.131084Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-05T00:30:46.131084Z", nullable=true)
 * )
 */
#[UseFactory(ModuleFactory::class)]
class Module extends BaseModel
{
    /** @use HasFactory<\Database\Factories\ModuleFactory> */
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function permissions(): HasMany {
        return $this->hasMany(Permission::class);
    }
}