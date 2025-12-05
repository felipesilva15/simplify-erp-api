<?php

namespace App\Modules\Auth\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="UpdateRoleRequest",
 *      required={"name"},
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=80),
 *      @OA\Property(property="description", type="string", example="Sample", minLength=1, maxLength=512, nullable=true)
 * )
 */
class UpdateRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:80',
            'description' => 'nullable|string|min:1|max:512'
        ];
    }
}