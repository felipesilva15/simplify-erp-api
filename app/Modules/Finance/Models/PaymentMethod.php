<?php

namespace App\Modules\Finance\Models;

use App\Modules\Finance\Enums\FinanceApplicabilityEnum;
use App\Modules\Finance\Enums\TaxTypeEnum;
use App\Core\Models\BaseModel;
use App\Modules\Finance\Enums\PaymentTypeEnum;
use Database\Factories\PaymentMethodFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="PaymentMethod",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="A vista"),
 *     @OA\Property(property="condition", type="string", example="Ato"),
 *     @OA\Property(property="payment_type", ref="#/components/schemas/PaymentTypeEnum"),
 *     @OA\Property(property="applicable_type", ref="#/components/schemas/FinanceApplicabilityEnum"),
 *     @OA\Property(property="tax_type", ref="#/components/schemas/TaxTypeEnum", nullable=true),
 *     @OA\Property(property="tax_aliquot", type="string", format="decimal", example=null, nullable=true),
 *     @OA\Property(property="tax_fixed_value", type="string", format="decimal", example=null, nullable=true),
 *     @OA\Property(property="penalty_aliquot", type="string", format="decimal", example=null, nullable=true),
 *     @OA\Property(property="interest_aliquot", type="string", format="decimal", example=null, nullable=true),
 *     @OA\Property(property="payment_deadline", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-11-23T17:34:08.292944Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-11-23T17:34:08.292944Z", nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-11-23T17:34:08.292944Z", nullable=true)
 * )
 */
#[UseFactory(PaymentMethodFactory::class)]
class PaymentMethod extends BaseModel
{
    /** @use HasFactory<\Database\Factories\PaymentMethodFactory> */
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'condition',
        'payment_type',
        'applicable_type',
        'tax_type',
        'tax_aliquot',
        'tax_fixed_value',
        'penalty_aliquot',
        'interest_aliquot',
        'payment_deadline'
    ];

    protected $casts = [
        'payment_type' => PaymentTypeEnum::class,
        'applicable_type' => FinanceApplicabilityEnum::class,
        'tax_type' => TaxTypeEnum::class,
        'tax_aliquot' => 'decimal:4',
        'tax_fixed_value' => 'decimal:2',
        'penalty_aliquot' => 'decimal:4',
        'interest_aliquot' => 'decimal:4',
    ];
}