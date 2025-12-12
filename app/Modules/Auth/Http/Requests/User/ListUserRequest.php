<?php

namespace App\Modules\Auth\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Core\Enums\SqlOrderDirectionEnum;

/**
 * @OA\Schema(
 *      schema="ListUserRequest",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample"),
 *      @OA\Property(property="email", type="string", example="Sample"),
 *      @OA\Property(property="email_verified_at", type="string", format="date-time", example="2025-12-11T11:13:04.482174Z", nullable=true),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-11T11:13:04.482174Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-11T11:13:04.482174Z", nullable=true),
 *      @OA\Property(property="username", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="phone_number", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-11T11:13:04.482174Z", nullable=true)
 * )
 */
class ListUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'email' => 'nullable|string',
            'email_verified_at' => 'nullable|datetime',
            'username' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'sort_by' => 'nullable|array',
            'sort_by.*' => 'string',
            'sort_dir' => 'nullable|array',
            'sort_dir.*' => [Rule::enum(SqlOrderDirectionEnum::class)],
            'per_page' => 'nullable|integer|max:100',
            'page' => 'nullable|integer|min:0'
        ];
    }
}
