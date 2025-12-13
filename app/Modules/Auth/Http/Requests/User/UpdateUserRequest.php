<?php

namespace App\Modules\Auth\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="UpdateUserRequest",
 *      required={"name","email","password","required"},
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=255),
 *      @OA\Property(property="email", type="string", example="Sample", minLength=1, maxLength=255),
 *      @OA\Property(property="username", type="string", example="Sample", minLength=1, maxLength=80, nullable=true),
 *      @OA\Property(property="phone_number", type="string", example="Sample", minLength=1, maxLength=14, nullable=true),
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
            'email' => "required|string|min:1|max:255|email|unique:users,email,{$this->email}",
            'username' => "required|string|min:2|max:80|unique:users,username,{$this->username}",
            'phone_number' => 'nullable|string|min:1|max:14',
            'roles' => 'array',
            'roles.*.id' => 'required|integer|exists:roles,id'
        ];
    }
}