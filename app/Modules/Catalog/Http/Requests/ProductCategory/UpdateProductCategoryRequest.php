<?php

namespace App\Modules\Catalog\Http\Requests\ProductCategory;

use App\Core\Enums\ProductApplicabilityEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="UpdateProductCategoryRequest",
 *     required={"name","applicability"},
 *     @OA\Property(property="name", type="string", example="Hidráulica", minLength=1, maxLength=120),
 *     @OA\Property(property="parent_category_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="applicability", ref="#/components/schemas/ProductApplicabilityEnum"),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 */
class UpdateProductCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:120',
            'parent_category_id' => 'nullable|integer|exists:product_categories,id',
            'applicability' => ['required', Rule::enum(ProductApplicabilityEnum::class)],
            'is_active' => 'boolean'
        ];
    }
}