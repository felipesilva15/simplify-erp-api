<?php

namespace App\Modules\Integration\Http\Requests\IntegrationConnection;

use Illuminate\Foundation\Http\FormRequest;

class StoreIntegrationConnectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:150'],
            'credentials' => ['required', 'array'],
            'settings' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'provider.required' => 'O provedor é obrigatório.',
            'name.required' => 'O nome é obrigatório.',
            'credentials.required' => 'As credenciais são obrigatórias.',
        ];
    }
}
