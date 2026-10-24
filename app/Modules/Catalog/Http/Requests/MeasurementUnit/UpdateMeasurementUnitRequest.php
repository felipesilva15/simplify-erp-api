<?php

namespace App\Modules\Catalog\Http\Requests\MeasurementUnit;

use App\Modules\Catalog\Enums\MeasurementUnitCategoryEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="UpdateMeasurementUnitRequest",
 *     required={"code","name","symbol","category"},
 *     @OA\Property(property="code", type="string", example="KG", minLength=1, maxLength=10),
 *     @OA\Property(property="name", type="string", example="Quilograma", minLength=1, maxLength=60),
 *     @OA\Property(property="symbol", type="string", example="kg", minLength=1, maxLength=10),
 *     @OA\Property(property="category", ref="#/components/schemas/MeasurementUnitCategoryEnum"),
 *     @OA\Property(property="base_unit_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="conversion_factor", type="number", format="float", example=1.0, minimum=0.00000001, maximum=9999999999.99999999),
 *     @OA\Property(property="decimal_places", type="integer", example=2, minimum=0, maximum=8),
 *     @OA\Property(property="external_code", type="string", example="KGM", minLength=1, maxLength=10, nullable=true),
 *     @OA\Property(property="is_system", type="boolean", example=false),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 */
class UpdateMeasurementUnitRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'min:1', 'max:10', Rule::unique('measurement_units', 'code')->ignore($this->route('measurement_unit'))->withoutTrashed()],
            'name' => 'required|string|min:1|max:60',
            'symbol' => 'required|string|min:1|max:10',
            'category' => ['required', Rule::enum(MeasurementUnitCategoryEnum::class)],
            'base_unit_id' => 'nullable|integer|exists:measurement_units,id',
            'conversion_factor' => 'nullable|numeric|gt:0',
            'decimal_places' => 'nullable|integer|min:0|max:8',
            'external_code' => 'nullable|string|min:1|max:10',
            'is_system' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
