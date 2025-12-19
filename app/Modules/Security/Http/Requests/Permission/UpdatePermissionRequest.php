<?php

namespace App\Modules\Security\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="UpdatePermissionRequest",
 *      required={"module_id","resource","action"},
 *      @OA\Property(property="module_id", type="integer", example=1),
 *      @OA\Property(property="resource", type="string", example="Sample", minLength=1, maxLength=60),
 *      @OA\Property(property="action", type="string", example="Sample", minLength=1, maxLength=120),
 *      @OA\Property(property="description", type="string", example="Sample", minLength=1, maxLength=512, nullable=true)
 * )
 */
class UpdatePermissionRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['name' => trim($this->resource).'.'.trim($this->action)]);
    }

    public function rules(): array
    {
        return [
            'module_id' => 'required|integer|exists:modules,id',
            'resource' => 'required|string|min:1|max:60',
            'action' => 'required|string|min:1|max:120',
            'name' => 'nullable',
            'description' => 'nullable|string|min:1|max:512'
        ];
    }
}