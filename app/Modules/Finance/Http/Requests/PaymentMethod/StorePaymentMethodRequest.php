<?php

namespace App\Modules\Finance\Http\Requests\PaymentMethod;

use App\Modules\Finance\Enums\FinanceApplicabilityEnum;
use App\Modules\Finance\Enums\PaymentTypeEnum;
use App\Modules\Finance\Enums\TaxTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="StorePaymentMethodRequest",
 *     required={"name","condition","payment_type","applicable_type","payment_deadline"},
 *     @OA\Property(property="name", type="string", example="A vista", minLength=1, maxLength=100),
 *     @OA\Property(property="condition", type="string", example="Ato", minLength=1, maxLength=80),
 *     @OA\Property(property="payment_type", ref="#/components/schemas/PaymentTypeEnum"),
 *     @OA\Property(property="applicable_type", ref="#/components/schemas/FinanceApplicabilityEnum"),
 *     @OA\Property(property="tax_type", ref="#/components/schemas/TaxTypeEnum", nullable=true),
 *     @OA\Property(property="tax_aliquot", type="string", format="decimal", example=null, minimum="0.0001", maximum="999.9999", nullable=true),
 *     @OA\Property(property="tax_fixed_value", type="string", format="decimal", example=null, minimum="0.01", maximum="9999999999999.99", nullable=true),
 *     @OA\Property(property="penalty_aliquot", type="string", format="decimal", example=null, minimum="0.0001", maximum="999.9999", nullable=true),
 *     @OA\Property(property="interest_aliquot", type="string", format="decimal", example=null, minimum="0.0001", maximum="999.9999", nullable=true),
 *     @OA\Property(property="payment_deadline", type="integer", example=1)
 * )
 */
class StorePaymentMethodRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:100',
            'condition' => 'required|string|min:1|max:80',
            'payment_type' => ['required', Rule::enum(PaymentTypeEnum::class)],
            'applicable_type' => ['required', Rule::enum(FinanceApplicabilityEnum::class)],
            'tax_type' => ['nullable', Rule::enum(TaxTypeEnum::class)],
            'tax_aliquot' => 'nullable|decimal:0,4',
            'tax_fixed_value' => 'nullable|decimal:0,2',
            'penalty_aliquot' => 'nullable|decimal:0,4',
            'interest_aliquot' => 'nullable|decimal:0,4',
            'payment_deadline' => 'required|integer'
        ];
    }
}