<?php

namespace App\Modules\Partner\Models;

use App\Core\Models\BaseModel;
use Database\Factories\PartnerTypeFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *      schema="PartnerType",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample"),
 *      @OA\Property(property="code", type="string", example="Sample"),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2026-09-05T06:33:15.515561Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2026-09-05T06:33:15.515561Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-09-05T06:33:15.515561Z", nullable=true)
 * )
 */
#[UseFactory(PartnerTypeFactory::class)]
class PartnerType extends BaseModel
{
    /** @use HasFactory<\Database\Factories\PartnerTypeFactory> */
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'code'
    ];

    protected $casts = [
        
    ];
}