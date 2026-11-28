<?php

namespace App\Modules\Inventory\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreWarehouseRequest",
 *     required={"name","is_active"},
 *     @OA\Property(property="name", type="string", example="Almoxarifado", minLength=1, maxLength=80),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 */
class StoreWarehouseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:80',
            'is_active' => 'required|boolean'
        ];
    }
}