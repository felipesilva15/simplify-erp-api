<?php

namespace App\Core\Models;

use App\Modules\Security\Models\Permission;
use Database\Factories\ResourceFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *      schema="Resource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample module"),
 *      @OA\Property(property="slug", type="string", example="sample"),
 *      @OA\Property(property="description", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="module_id", type="integer", example=1),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-05T00:30:46.131084Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-05T00:30:46.131084Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-05T00:30:46.131084Z", nullable=true)
 * )
 */
#[UseFactory(ResourceFactory::class)]
class Resource extends BaseModel
{
    /** @use HasFactory<\Database\Factories\ResourceFactory> */
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'module_id'
    ];

    public function module(): BelongsTo {
        return $this->belongsTo(Module::class);
    }

    public function permissions(): HasMany {
        return $this->hasMany(Permission::class);
    }
}