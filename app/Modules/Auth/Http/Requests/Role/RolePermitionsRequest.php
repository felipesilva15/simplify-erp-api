<?php

namespace App\Modules\Auth\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Core\Enums\SqlOrderDirectionEnum;

/**
 * @OA\Schema(
 *      schema="RolePermitionsRequest",
 *      @OA\Property(property="ids", type="array", @OA\Items(type="integer")),
 * )
 */
class RolePermitionsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => ['array'],
            'ids.*' => 'integer|exists:permitions,id'
        ];
    }
}
