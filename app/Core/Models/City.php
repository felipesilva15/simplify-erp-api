<?php

namespace App\Core\Models;

use App\Core\Models\BaseModel;
use Database\Factories\CityFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="City",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="state_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Sample"),
 *     @OA\Property(property="ibge_code", type="string", example="Sample"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-09-24T17:20:04.068390Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-09-24T17:20:04.068390Z", nullable=true)
 * )
 */
#[UseFactory(CityFactory::class)]
class City extends BaseModel
{
    /** @use HasFactory<\Database\Factories\CityFactory> */
    use HasFactory;

    protected $fillable = [
        'state_id',
        'name',
        'ibge_code'
    ];

    protected $casts = [
        
    ];

    public function state(): BelongsTo {
        return $this->belongsTo(State::class);
    }
}