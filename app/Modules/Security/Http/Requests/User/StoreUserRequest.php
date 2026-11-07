<?php

namespace App\Modules\Security\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * @OA\Schema(
 *      schema="StoreUserRequest",
 *      required={"name","email","password","username","is_admin"},
 *
 *      @OA\Property(property="name", type="string", example="Felipe Silva", minLength=1, maxLength=255),
 *      @OA\Property(property="email", type="string", format="email", example="felipe.silva@simplify.com.br", minLength=1, maxLength=255),
 *      @OA\Property(property="password", type="string", example="Senha@123", minLength=1, maxLength=255),
 *      @OA\Property(property="username", type="string", example="felipe.silva", minLength=1, maxLength=80),
 *      @OA\Property(property="phone_number", type="string", example="5511985841589", minLength=1, maxLength=14, nullable=true),
 *      @OA\Property(property="is_admin", type="boolean", example=false),
 *      @OA\Property(
 *          property="roles",
 *          type="array",
 *          example={{"id": 1}},
 *
 *          @OA\Items(
 *              required={"id"},
 *
 *              @OA\Property(property="id", type="integer", example=1)
 *          )
 *      )
 * )
 */
class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:255',
            'email' => 'required|string|min:1|max:255|email|unique:users,email',
            'password' => ['required', Password::defaults()],
            'username' => 'required|string|min:2|max:80|unique:users,username',
            'phone_number' => 'nullable|string|min:1|max:14',
            'is_admin' => 'required|boolean',
            'roles' => 'array',
            'roles.*.id' => 'required|integer|exists:roles,id',
        ];
    }
}
