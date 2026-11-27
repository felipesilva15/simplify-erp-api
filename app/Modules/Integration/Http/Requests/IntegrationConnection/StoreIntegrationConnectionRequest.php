<?php

namespace App\Modules\Integration\Http\Requests\IntegrationConnection;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="StoreIntegrationConnectionRequest",
 *     required={"provider","name","credentials"},
 *
 *     @OA\Property(property="provider", type="string", example="billing", maxLength=100),
 *     @OA\Property(property="name", type="string", example="Conexão ERP Produção", maxLength=150),
 *     @OA\Property(property="credentials", type="object", example={"api_key":"sk_live_1234567890"}),
 *     @OA\Property(property="settings", type="object", nullable=true, example={"base_url":"https://api.exemplo.com"}),
 *     @OA\Property(property="is_active", type="boolean", example=true, nullable=true)
 * )
 */
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
