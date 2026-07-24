<?php

namespace App\Modules\Security\Models;

use App\Core\Models\BaseModel;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *      schema="Role",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample"),
 *      @OA\Property(property="description", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-01T11:21:13.278086Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-01T11:21:13.278086Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-01T11:21:13.278086Z", nullable=true)
 * )
 */
#[UseFactory(RoleFactory::class)]
class Role extends BaseModel
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    protected $casts = [
        
    ];

    public function permissions(): BelongsToMany {
        return $this->belongsToMany(Permission::class)
                    ->using(PermissionRole::class)
                    ->withTimestamps();
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class)
                    ->using(PermissionRole::class)
                    ->withTimestamps();
    }
}