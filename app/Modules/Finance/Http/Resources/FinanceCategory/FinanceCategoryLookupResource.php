<?php

namespace App\Modules\Finance\Http\Resources\FinanceCategory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="FinanceCategoryLookupResource",
 *      @OA\Property(property="key", type="string", example="1"),
 *      @OA\Property(property="label", type="string", example="Materiais", minLength=1, maxLength=100),
 *      @OA\Property(property="sublabel", type="string", example="Cód.: 1", minLength=1, maxLength=80),
 *      @OA\Property(
 *          property="meta",
 *          type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Materiais", minLength=1, maxLength=100),
 *         @OA\Property(property="code", type="string", example="1", minLength=1, maxLength=20),
 *         @OA\Property(property="applicable_type", ref="#/components/schemas/FinanceApplicabilityEnum")
 *      )
 * )
 */
class FinanceCategoryLookupResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'key' => $this->id,
            'label' => $this->name,
            'sublabel' => "Cód.: {$this->id}",
            'meta' => $this->only('id', 'name', 'code', 'applicable_type')
        ];
    }
}