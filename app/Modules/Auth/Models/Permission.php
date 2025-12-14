<?php

namespace App\Modules\Auth\Models;

use App\Core\Models\Module;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *      schema="Permission",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="module_id", type="integer", example=1),
 *      @OA\Property(property="resource", type="string", example="Sample"),
 *      @OA\Property(property="action", type="string", example="Sample"),
 *      @OA\Property(property="name", type="string", example="Sample"),
 *      @OA\Property(property="description", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-05T00:27:48.633261Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-05T00:27:48.633261Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-05T00:27:48.633261Z", nullable=true)
 * )
 */
class Permission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'module_id',
        'resource',
        'action',
        'name',
        'description'
    ];

    protected $casts = [
        
    ];

    public function module(): BelongsTo {
        return $this->belongsTo(Module::class);
    }

    public function roles(): BelongsToMany {
        return $this->belongsToMany(Role::class)
                    ->using(PermissionRole::class);
    }
}