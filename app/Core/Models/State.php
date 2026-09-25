<?php

namespace App\Core\Models;

use App\Core\Models\BaseModel;
use Database\Factories\StateFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="State",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="country_id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Sample"),
 *     @OA\Property(property="uf", type="string", example="Sample"),
 *     @OA\Property(property="ibge_code", type="string", example="Sample"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-09-24T04:09:20.641277Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-09-24T04:09:20.641277Z", nullable=true)
 * )
 */
#[UseFactory(StateFactory::class)]
class State extends BaseModel
{
    /** @use HasFactory<\Database\Factories\StateFactory> */
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
        'uf',
        'ibge_code'
    ];

    protected $casts = [
        
    ];

    public function country(): BelongsTo {
        return $this->belongsTo(Country::class);
    }
}