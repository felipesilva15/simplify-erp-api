<?php

namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
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
class Role extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description'
    ];

    protected $casts = [
        
    ];

    public function permitions(): BelongsToMany {
        return $this->belongsToMany(Permition::class)
                    ->using(PermitionRole::class)
                    ->withTimestamps();
    }
}