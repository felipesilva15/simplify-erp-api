<?php

namespace App\Core\Models;

use App\Core\Models\BaseModel;
use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Country",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="iso_code", type="string", example="Sample"),
 *     @OA\Property(property="name", type="string", example="Sample"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-09-24T03:46:27.255709Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-09-24T03:46:27.255709Z", nullable=true)
 * )
 */
#[UseFactory(CountryFactory::class)]
class Country extends BaseModel
{
    /** @use HasFactory<\Database\Factories\CountryFactory> */
    use HasFactory;

    protected $fillable = [
        'iso_code',
        'name'
    ];

    protected $casts = [
        
    ];
}