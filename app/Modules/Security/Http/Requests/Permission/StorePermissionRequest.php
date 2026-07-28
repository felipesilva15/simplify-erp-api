<?php

namespace App\Modules\Security\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="StorePermissionRequest",
 *      required={"resource_id","label","action"},
 *      @OA\Property(property="resource_id", type="integer", example=1),
 *      @OA\Property(property="label", type="string", example="Sample", minLength=1, maxLength=60),
 *      @OA\Property(property="action", type="string", example="Sample", minLength=1, maxLength=120),
 *      @OA\Property(property="description", type="string", example="Sample", minLength=1, maxLength=512, nullable=true)
 * )
 */
class StorePermissionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'resource_id' => 'required|integer|exists:resources,id',
            'label' => 'required|string|min:1|max:80',
            'action' => 'required|string|min:1|max:120',
            'description' => 'nullable|string|min:1|max:512'
        ];
    }
}