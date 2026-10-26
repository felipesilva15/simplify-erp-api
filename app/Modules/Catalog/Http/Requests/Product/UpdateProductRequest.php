<?php

namespace App\Modules\Catalog\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateProductRequest",
 *     required={"type","item_type_code","sku","name","price","cost","is_active","measurement_unit_id"},
 *     @OA\Property(property="type", type="string", example="Sample", minLength=1, maxLength=20),
 *     @OA\Property(property="item_type_code", type="string", example="Sample", minLength=2, maxLength=2),
 *     @OA\Property(property="sku", type="string", example="Sample", minLength=1, maxLength=40),
 *     @OA\Property(property="gtin", type="string", example="Sample", minLength=1, maxLength=14, nullable=true),
 *     @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=255),
 *     @OA\Property(property="description", type="string", example="Sample", minLength=1, maxLength=1024, nullable=true),
 *     @OA\Property(property="notes", type="string", example="Sample", minLength=1, maxLength=0, nullable=true),
 *     @OA\Property(property="price", type="number", format="float", example=20.99, minimum=0.0001, maximum=99999999999999.9999),
 *     @OA\Property(property="cost", type="number", format="float", example=20.99, minimum=0.0001, maximum=99999999999999.9999),
 *     @OA\Property(property="net_weight", type="number", format="float", example=20.99, minimum=0.001, maximum=999999999.999, nullable=true),
 *     @OA\Property(property="gross_weight", type="number", format="float", example=20.99, minimum=0.001, maximum=999999999.999, nullable=true),
 *     @OA\Property(property="width", type="number", format="float", example=20.99, minimum=0.0001, maximum=99999999.9999, nullable=true),
 *     @OA\Property(property="height", type="number", format="float", example=20.99, minimum=0.0001, maximum=99999999.9999, nullable=true),
 *     @OA\Property(property="length", type="number", format="float", example=20.99, minimum=0.0001, maximum=99999999.9999, nullable=true),
 *     @OA\Property(property="minimum_stock", type="number", format="float", example=20.99, minimum=0.0001, maximum=99999999999999.9999, nullable=true),
 *     @OA\Property(property="maximum_stock", type="number", format="float", example=20.99, minimum=0.0001, maximum=99999999999999.9999, nullable=true),
 *     @OA\Property(property="is_active", type="boolean", example=false),
 *     @OA\Property(property="measurement_unit_id", type="integer", example=1),
 *     @OA\Property(property="product_category_id", type="integer", example=1, nullable=true),
 *     @OA\Property(property="product_brand_id", type="integer", example=1, nullable=true)
 * )
 */
class UpdateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'type' => 'required|string|min:1|max:20',
            'item_type_code' => 'required|string|min:2|max:2',
            'sku' => 'required|string|min:1|max:40',
            'gtin' => 'nullable|string|min:1|max:14',
            'name' => 'required|string|min:1|max:255',
            'description' => 'nullable|string|min:1|max:1024',
            'notes' => 'nullable|string',
            'price' => 'required|decimal:4',
            'cost' => 'required|decimal:4',
            'net_weight' => 'nullable|decimal:3',
            'gross_weight' => 'nullable|decimal:3',
            'width' => 'nullable|decimal:4',
            'height' => 'nullable|decimal:4',
            'length' => 'nullable|decimal:4',
            'minimum_stock' => 'nullable|decimal:4',
            'maximum_stock' => 'nullable|decimal:4',
            'is_active' => 'required|boolean',
            'measurement_unit_id' => 'required|integer',
            'product_category_id' => 'nullable|integer',
            'product_brand_id' => 'nullable|integer'
        ];
    }
}