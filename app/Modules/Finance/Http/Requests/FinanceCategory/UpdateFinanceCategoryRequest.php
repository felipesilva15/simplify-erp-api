<?php

namespace App\Modules\Finance\Http\Requests\FinanceCategory;
use App\Modules\Finance\Enums\FinanceApplicabilityEnum;
use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateFinanceCategoryRequest",
 *     required={"code","name","applicable_type"},
 *     @OA\Property(property="parent_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="code", type="string", example="1", minLength=1, maxLength=20),
 *     @OA\Property(property="name", type="string", example="Materiais", minLength=1, maxLength=100),
 *     @OA\Property(property="applicable_type", ref="#/components/schemas/FinanceApplicabilityEnum")
 * )
 */
class UpdateFinanceCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('finance_category');

        return [
            'parent_id' => 'nullable|integer|exists:finance_categories,id',
            'code' => ['required', 'string', 'min:1', 'max:20', Rule::unique('finance_categories', 'code')->ignore($id)->withoutTrashed()],
            'name' => 'required|string|min:1|max:100',
            'applicable_type' => ['required', Rule::enum(FinanceApplicabilityEnum::class)]
        ];
    }
}