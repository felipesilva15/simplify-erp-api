<?php

namespace App\Modules\Integration\Http\Requests\IntegrationConnection;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="UpdateIntegrationConnectionRequest",
 *     @OA\Property(property="provider", type="string"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="credentials", type="object"),
 *     @OA\Property(property="settings", type="object", nullable=true),
 *     @OA\Property(property="is_active", type="boolean", nullable=true)
 * )
 */
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
