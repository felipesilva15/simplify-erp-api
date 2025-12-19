<?php

namespace App\Modules\Security\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *      schema="UpdateUserRequest",
 *      required={"name","email","password","required"},
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=255),
 *      @OA\Property(property="email", type="string", example="Sample", minLength=1, maxLength=255),
 *      @OA\Property(property="username", type="string", example="Sample", minLength=1, maxLength=80, nullable=true),
 *      @OA\Property(property="phone_number", type="string", example="Sample", minLength=1, maxLength=14, nullable=true),
 *      @OA\Property(property="is_admin", type="boolean", example=false),
 *      @OA\Property(
 *          property="roles", 
 *          type="array", 
 *          @OA\Items(
 *              required={"id"},
 *              @OA\Property(property="id", type="integer", example=1)
 *          ) 
 *      )
 * )
 */
class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:255',
            'email' => ['required', 'string', 'min:1', 'max:255', 'email', Rule::unique('users', 'email')->ignore($this->user)],
            'username' => ['required', 'string', 'min:2', 'max:80', Rule::unique('users', 'username')->ignore($this->user)],
            'phone_number' => 'nullable|string|min:1|max:14',
            'is_admin' => 'required|boolean',
            'roles' => 'array',
            'roles.*.id' => 'required|integer|exists:roles,id'
        ];
    }
}