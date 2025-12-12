<?php

namespace App\Modules\Auth\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * @OA\Schema(
 *      schema="StoreUserRequest",
 *      required={"name","email","password","username"},
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=255),
 *      @OA\Property(property="email", type="string", example="Sample", minLength=1, maxLength=255),
 *      @OA\Property(property="password", type="string", example="Sample", minLength=1, maxLength=255),
 *      @OA\Property(property="username", type="string", example="Sample", minLength=1, maxLength=80, nullable=true),
 *      @OA\Property(property="phone_number", type="string", example="Sample", minLength=1, maxLength=14, nullable=true)
 * )
 */
class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:255',
            'email' => "required|string|min:1|max:255|email|unique:users,email",
            'password' => ['required', Password::defaults()],
            'username' => 'required|string|min:2|max:80|unique:users,username',
            'phone_number' => 'nullable|string|min:1|max:14'
        ];
    }
}