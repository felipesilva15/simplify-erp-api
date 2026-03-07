<?php

namespace App\Modules\Security\Http\Requests\Permission;

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
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-05T00:27:48.907847Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-05T00:27:48.907847Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-05T00:27:48.907847Z", nullable=true)
 * )
 */
class ListPermissionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'module_id' => 'nullable|integer',
            'resource' => 'nullable|string',
            'action' => 'nullable|string',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'sorts' => 'nullable|string',
            'per_page' => 'nullable|integer|max:100',
            'page' => 'nullable|integer|min:0'
        ];
    }
}
