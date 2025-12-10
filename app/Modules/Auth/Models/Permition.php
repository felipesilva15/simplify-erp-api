<?php

namespace App\Modules\Auth\Models;

use App\Core\Models\Module;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *      schema="Permition",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="module_id", type="integer", example=1),
 *      @OA\Property(property="group", type="string", example="Sample"),
 *      @OA\Property(property="action", type="string", example="Sample"),
 *      @OA\Property(property="description", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="has_access_free", type="boolean", example=false, nullable=true),
 *      @OA\Property(property="is_active", type="boolean", example=false, nullable=true),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-05T00:27:48.633261Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-05T00:27:48.633261Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-05T00:27:48.633261Z", nullable=true)
 * )
 */
class Permition extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'module_id',
        'group',
        'action',
        'description',
        'has_access_free',
        'is_active'
    ];

    protected $casts = [
        'has_access_free' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function module(): BelongsTo {
        return $this->belongsTo(Module::class);
    }
}