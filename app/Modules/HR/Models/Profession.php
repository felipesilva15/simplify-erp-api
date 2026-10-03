<?php

namespace App\Modules\HR\Models;

use App\Core\Models\BaseModel;
use Database\Factories\ProfessionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Profession",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="cbo", type="string", example="Sample"),
 *     @OA\Property(property="name", type="string", example="Sample"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-10-01T12:41:58.875427Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-10-01T12:41:58.875427Z", nullable=true)
 * )
 */
#[UseFactory(ProfessionFactory::class)]
class Profession extends BaseModel
{
    /** @use HasFactory<\Database\Factories\ProfessionFactory> */
    use HasFactory;

    protected $fillable = [
        
    ];

    protected $casts = [
        
    ];
}