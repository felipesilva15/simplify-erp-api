<?php

namespace App\Modules\Security\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Module;
use App\Core\Models\Resource;
use Database\Factories\PermissionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *      schema="Permission",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="resource_id", type="integer", example=1),
 *      @OA\Property(property="label", type="string", example="Sample"),
 *      @OA\Property(property="action", type="string", example="Sample"),
 *      @OA\Property(property="name", type="string", example="Sample"),
 *      @OA\Property(property="description", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-05T00:27:48.633261Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-05T00:27:48.633261Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-05T00:27:48.633261Z", nullable=true)
 * )
 */
#[UseFactory(PermissionFactory::class)]
class Permission extends BaseModel
{
    /** @use HasFactory<\Database\Factories\PermissionFactory> */
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'resource_id',
        'action',
        'name',
        'label',
        'description'
    ];

    protected $casts = [
        
    ];

    public function resource(): BelongsTo {
        return $this->belongsTo(Resource::class);
    }

    public function roles(): BelongsToMany {
        return $this->belongsToMany(Role::class)
                    ->using(PermissionRole::class);
    }
}