<?php

namespace App\Modules\Partner\Http\Requests\PartnerType;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="StorePartnerTypeRequest",
 *      required={"name","code"},
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=60),
 *      @OA\Property(property="code", type="string", example="Sample", minLength=1, maxLength=3)
 * )
 */
class StorePartnerTypeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:60',
            'code' => 'required|string|min:1|max:3|unique:partner_types,code'
        ];
    }
}