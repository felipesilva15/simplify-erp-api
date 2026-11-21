<?php

namespace App\Modules\Catalog\Http\Requests\Service;

use App\Core\Enums\ProductTypeEnum;
use App\Modules\Catalog\Enums\ItemTypeCodeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="StoreServiceRequest",
 *     required={"item_type_code","sku","name","price","cost","is_active","measurement_unit_id"},
 *
 *     @OA\Property(property="item_type_code", ref="#/components/schemas/ItemTypeCodeEnum", description="Códigos 09, 10 e 99"),
 *     @OA\Property(property="sku", type="string", example="SERV-001", minLength=1, maxLength=40),
 *     @OA\Property(property="name", type="string", example="Consultoria em sistemas", minLength=1, maxLength=255),
 *     @OA\Property(property="description", type="string", example="Consultoria técnica especializada", minLength=1, maxLength=1024, nullable=true),
 *     @OA\Property(property="notes", type="string", example="Cobrança por hora", nullable=true),
 *     @OA\Property(property="price", type="string", format="decimal", example="150.00", minimum=0.0001, maximum=99999999999999.9999),
 *     @OA\Property(property="cost", type="string", format="decimal", example="80.00", minimum=0.0001, maximum=99999999999999.9999),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="measurement_unit_id", type="integer", example=1),
 *     @OA\Property(property="product_category_id", type="integer", example=1, nullable=true)
 * )
 */
class StoreServiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'item_type_code' => [
                'required',
                'string',
                'size:2',
                Rule::in(ItemTypeCodeEnum::valuesFor(ProductTypeEnum::Service)),
            ],
            'sku' => ['required', 'string', 'min:1', 'max:40', Rule::unique('products', 'sku')->withoutTrashed()],
            'name' => 'required|string|min:1|max:255',
            'description' => 'nullable|string|min:1|max:1024',
            'notes' => 'nullable|string',
            'price' => 'required|decimal:0,4',
            'cost' => 'required|decimal:0,4',
            'is_active' => 'required|boolean',
            'measurement_unit_id' => 'required|integer|exists:measurement_units,id',
            'product_category_id' => 'nullable|integer|exists:product_categories,id',
        ];
    }
}
