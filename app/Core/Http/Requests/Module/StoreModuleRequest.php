<?php

namespace App\Core\Http\Requests\Module;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="StoreModuleRequest",
 *      required={"name","slug"},
 *      @OA\Property(property="name", type="string", example="Sample module", minLength=1, maxLength=80),
 *      @OA\Property(property="slug", type="string", example="sample", minLength=1, maxLength=80),
 *      @OA\Property(property="description", type="string", example="Sample", minLength=1, maxLength=512, nullable=true),
 *      @OA\Property(property="is_active", type="boolean", example=false, nullable=true)
 * )
 */
class StoreModuleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:80',
            'slug' => 'required|string|min:1|max:80',
            'description' => 'nullable|string|min:1|max:512',
            'is_active' => 'nullable|boolean'
        ];
    }
}