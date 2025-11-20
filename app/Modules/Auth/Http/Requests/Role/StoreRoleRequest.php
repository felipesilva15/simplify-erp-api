<?php

namespace App\Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'status' => 'nullable|string'
        ];
    }
}