<?php

namespace App\Modules\Integration\Http\Requests\IntegrationConnection;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIntegrationConnectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => ['sometimes', 'required', 'string', 'max:100'],
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'credentials' => ['sometimes', 'array'], // write-only
            'settings' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
