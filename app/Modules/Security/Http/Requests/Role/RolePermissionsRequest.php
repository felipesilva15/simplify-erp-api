<?php

namespace App\Modules\Security\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="RolePermissionsRequest",
 *
 *      @OA\Property(property="ids", type="array", example={1, 2, 3}, @OA\Items(type="integer")),
 * )
 */
class RolePermissionsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => ['array'],
            'ids.*' => 'integer|exists:permissions,id',
        ];
    }
}
