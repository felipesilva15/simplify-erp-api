<?php

namespace App\Modules\Finance\Models;

use App\Core\Models\BaseModel;
use App\Modules\Finance\Enums\FinanceApplicabilityEnum;
use Database\Factories\FinanceCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="FinanceCategory",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="parent_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="code", type="string", example="1"),
 *     @OA\Property(property="name", type="string", example="Materiais"),
 *     @OA\Property(property="applicable_type", ref="#/components/schemas/FinanceApplicabilityEnum"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-11-27T22:31:29.964450Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-11-27T22:31:29.964450Z", nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-11-27T22:31:29.964450Z", nullable=true)
 * )
 */
#[UseFactory(FinanceCategoryFactory::class)]
class FinanceCategory extends BaseModel
{
    /** @use HasFactory<\Database\Factories\FinanceCategoryFactory> */
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'parent_id',
        'code',
        'name',
        'applicable_type'
    ];

    protected $casts = [
        'applicable_type' => FinanceApplicabilityEnum::class
    ];
}