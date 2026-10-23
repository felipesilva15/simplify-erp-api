<?php

namespace App\Modules\Catalog\Http\Requests\ProductBrand;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateProductBrandRequest",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="Hauwei", minLength=1, maxLength=120)
 * )
 */
class UpdateProductBrandRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:120'
        ];
    }
}