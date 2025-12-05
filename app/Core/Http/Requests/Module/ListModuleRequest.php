<?php

namespace App\Core\Http\Requests\Module;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Core\Enums\SqlOrderDirectionEnum;

/**
 * @OA\Schema(
 *      schema="ListModuleRequest",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample"),
 *      @OA\Property(property="description", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="is_active", type="boolean", example=false, nullable=true),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-05T00:30:46.141280Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-05T00:30:46.141280Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-05T00:30:46.141280Z", nullable=true)
 * )
 */
class ListModuleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'sort_by' => 'nullable|array',
            'sort_by.*' => 'string',
            'sort_dir' => 'nullable|array',
            'sort_dir.*' => [Rule::enum(SqlOrderDirectionEnum::class)],
            'per_page' => 'nullable|integer|max:100',
            'page' => 'nullable|integer|min:0'
        ];
    }
}
