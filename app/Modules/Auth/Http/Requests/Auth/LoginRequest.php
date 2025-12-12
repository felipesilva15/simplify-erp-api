<?php

namespace App\Modules\Auth\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *      schema="LoginRequest",
 *      required={"username", "password"},
 *      @OA\Property(property="username", type="string", example="felipe.silva"),
 *      @OA\Property(property="password", type="string", example="123")
 * )
 */
class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => 'required|string',
            'password' => 'required|string'
        ];
    }
}
