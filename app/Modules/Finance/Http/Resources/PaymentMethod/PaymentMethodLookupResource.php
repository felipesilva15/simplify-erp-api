<?php

namespace App\Modules\Finance\Http\Resources\PaymentMethod;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="PaymentMethodLookupResource",
 *      @OA\Property(property="key", type="integer", example=1),
 *      @OA\Property(property="label", type="string", example="A vista", minLength=1, maxLength=100),
 *      @OA\Property(property="sublabel", type="string", example="Cód.: 1", minLength=1, maxLength=80),
 *      @OA\Property(
 *          property="meta",
 *          type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="A vista", minLength=1, maxLength=100),
 *         @OA\Property(property="condition", type="string", example="Ato", minLength=1, maxLength=80),
 *         @OA\Property(property="applicable_type", ref="#/components/schemas/FinanceApplicabilityEnum")
 *      )
 * )
 */
class PaymentMethodLookupResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'key' => $this->id,
            'label' => $this->name,
            'sublabel' => "Cód.: {$this->id}",
            'meta' => $this->only('id', 'name', 'condition', 'applicable_type')
        ];
    }
}