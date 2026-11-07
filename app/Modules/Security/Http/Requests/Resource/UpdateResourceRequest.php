<?php

namespace App\Modules\Security\Http\Requests\Resource;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="UpdateResourceRequest",
 *      required={"name","slug","module_id"},
 *
 *      @OA\Property(property="name", type="string", example="Usuários", minLength=1, maxLength=80),
 *      @OA\Property(property="slug", type="string", example="users", minLength=1, maxLength=80),
 *      @OA\Property(property="description", type="string", example="Recursos de usuários", minLength=1, maxLength=512, nullable=true),
 *      @OA\Property(property="module_id", type="integer", example=1)
 * )
 */
class UpdateResourceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:80',
            'slug' => 'required|string|min:1|max:80',
            'description' => 'nullable|string|min:1|max:512',
            'module_id' => 'required|integer|exists:modules,id',
        ];
    }
}
