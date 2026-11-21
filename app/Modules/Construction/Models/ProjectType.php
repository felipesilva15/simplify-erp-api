<?php

namespace App\Modules\Construction\Models;

use App\Core\Models\BaseModel;
use Database\Factories\ProjectTypeFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="ProjectType",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Sample"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-11-21T23:11:04.640320Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-11-21T23:11:04.640320Z", nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-11-21T23:11:04.640320Z", nullable=true)
 * )
 */
#[UseFactory(ProjectTypeFactory::class)]
class ProjectType extends BaseModel
{
    /** @use HasFactory<\Database\Factories\ProjectTypeFactory> */
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name'
    ];

    protected $casts = [
        
    ];
}