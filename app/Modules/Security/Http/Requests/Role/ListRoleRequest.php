<?php

namespace App\Modules\Security\Http\Requests\Role;

use App\Core\Enums\RequestQueryOperatorsEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Core\Enums\SqlOrderDirectionEnum;

class ListRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'filters' => 'nullable|array',
            'filters.*' => 'array',
            'filters.*.*' => 'required',
            'sorts' => 'nullable|string',
            'per_page' => 'nullable|integer|max:100',
            'page' => 'nullable|integer|min:0'
        ];
    }
}
