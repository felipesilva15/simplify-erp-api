<?php

namespace App\Modules\Auth\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Core\Enums\SqlOrderDirectionEnum;

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
