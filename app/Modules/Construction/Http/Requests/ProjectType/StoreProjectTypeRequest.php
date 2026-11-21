<?php

namespace App\Modules\Construction\Http\Requests\ProjectType;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreProjectTypeRequest",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="Reforma", minLength=1, maxLength=80)
 * )
 */
class StoreProjectTypeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:80'
        ];
    }
}