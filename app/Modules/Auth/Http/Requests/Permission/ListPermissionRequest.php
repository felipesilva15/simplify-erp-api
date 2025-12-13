<?php

namespace App\Modules\Auth\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Core\Enums\SqlOrderDirectionEnum;

/**
 * @OA\Schema(
 *      schema="ListPermissionRequest",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="module_id", type="integer", example=1),
 *      @OA\Property(property="resource", type="string", example="Sample"),
 *      @OA\Property(property="action", type="string", example="Sample"),
 *      @OA\Property(property="name", type="string", example="Sample"),
 *      @OA\Property(property="description", type="string", example="Sample", nullable=true),
 *      @OA\Property(property="has_access_free", type="boolean", example=false, nullable=true),
 *      @OA\Property(property="is_active", type="boolean", example=false, nullable=true),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-05T00:27:48.907847Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-05T00:27:48.907847Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-05T00:27:48.907847Z", nullable=true)
 * )
 */
class ListPermissionRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (isset($this->has_access_free))
            $this->merge(['has_access_free' => filter_var($this->has_access_free, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)]);

        if (isset($this->is_active))
            $this->merge(['is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)]);
    }

    public function rules(): array
    {
        return [
            'module_id' => 'nullable|integer',
            'resource' => 'nullable|string',
            'action' => 'nullable|string',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'has_access_free' => 'nullable|boolean',
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
