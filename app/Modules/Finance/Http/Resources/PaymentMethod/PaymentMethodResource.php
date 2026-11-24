<?php

namespace App\Modules\Finance\Http\Resources\PaymentMethod;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="PaymentMethodResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="A vista", minLength=1, maxLength=100),
 *     @OA\Property(property="condition", type="string", example="Ato", minLength=1, maxLength=80),
 *     @OA\Property(property="payment_type", ref="#/components/schemas/PaymentTypeEnum"),
 *     @OA\Property(property="applicable_type", ref="#/components/schemas/FinanceApplicabilityEnum"),
 *     @OA\Property(property="tax_type", ref="#/components/schemas/TaxTypeEnum", nullable=true),
 *     @OA\Property(property="tax_aliquot", type="string", format="decimal", example=null, minimum="0.0001", maximum="999.9999", nullable=true),
 *     @OA\Property(property="tax_fixed_value", type="string", format="decimal", example=null, minimum="0.01", maximum="9999999999999.99", nullable=true),
 *     @OA\Property(property="penalty_aliquot", type="string", format="decimal", example=null, minimum="0.0001", maximum="999.9999", nullable=true),
 *     @OA\Property(property="interest_aliquot", type="string", format="decimal", example=null, minimum="0.0001", maximum="999.9999", nullable=true),
 *     @OA\Property(property="payment_deadline", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2026-11-23T17:34:08.292944Z", nullable=true),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-11-23T17:34:08.292944Z", nullable=true),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2026-11-23T17:34:08.292944Z", nullable=true)
 * )
 */
class PaymentMethodResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'condition' => $this->condition,
            'applicable_type' => $this->applicable_type,
            'tax_type' => $this->tax_type,
            'tax_aliquot' => $this->tax_aliquot,
            'tax_fixed_value' => $this->tax_fixed_value,
            'payment_deadline' => $this->payment_deadline,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}