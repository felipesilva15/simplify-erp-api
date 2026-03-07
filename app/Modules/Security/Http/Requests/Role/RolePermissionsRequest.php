<?php

namespace App\Modules\Security\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="RolePermissionsRequest",
 *      @OA\Property(property="ids", type="array", @OA\Items(type="integer")),
 * )
 */
class RolePermissionsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => ['array'],
            'ids.*' => 'integer|exists:permissions,id'
        ];
    }
}
