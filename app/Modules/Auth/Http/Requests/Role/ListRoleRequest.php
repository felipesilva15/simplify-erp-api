<?php

namespace App\Modules\Auth\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Core\Enums\SqlOrderDirectionEnum;

/**
 * @OA\Schema(
 *      schema="ListRoleRequest",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample"),
 *      @OA\Property(property="description", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-01T11:21:13.539443Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-01T11:21:13.539443Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-01T11:21:13.539443Z", nullable=true)
 * )
 */
class ListRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'sort_by' => 'nullable|array',
            'sort_by.*' => 'string',
            'sort_dir' => 'nullable|array',
            'sort_dir.*' => [Rule::enum(SqlOrderDirectionEnum::class)],
            'per_page' => 'nullable|integer|max:100',
            'page' => 'nullable|integer|min:0'
        ];
    }
}
