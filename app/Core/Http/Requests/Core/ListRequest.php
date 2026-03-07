<?php

namespace App\Core\Http\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Parameter(parameter="sortsParam", name="sorts", in="query", required=false, example="-id", description="Fields for sorting separated by commas. Use '-' to sort descending", @OA\Schema(type="string")),
 * @OA\Parameter(parameter="perPageParam", name="per_page", in="query", required=false, example=10, description="Items per page", @OA\Schema(type="integer")),
 * @OA\Parameter(parameter="pageParam", name="page", in="query", required=false, example=1, description="Page number", @OA\Schema(type="integer")),
 * @OA\Schema(
 *     schema="ListRequest",
 *     type="object",
 *     @OA\Property(
 *         property="filters",
 *         ref="#/components/schemas/Filters",
 *         example={
 *             "id": {"gte": 1, "lte": 10},
 *             "name": {"like": "Felipe"}
 *         }
 *     ),
 *     @OA\Property(property="sorts", type="string", example="-id", description="Fields for sorting separated by commas. Use '-' to sort descending"),
 *     @OA\Property(property="per_page", type="integer", example=10, description="Items per page"),
 *     @OA\Property(property="page", type="integer", example=1, description="Page number")
 * )
 */
class ListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'filters' => 'nullable|array',
            'filters.*' => 'array',
            'filters.*.*' => 'required',
            'sorts' => 'nullable|string',
            'per_page' => 'nullable|integer|max:100',
            'page' => 'nullable|integer|min:1'
        ];
    }
}
