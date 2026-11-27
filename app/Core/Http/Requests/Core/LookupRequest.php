<?php

namespace App\Core\Http\Requests\Core;

/**
 * @OA\Parameter(parameter="qParam", name="q", in="query", required=false, example="", description="Free text search across the searchable columns", @OA\Schema(type="string")),
 * @OA\Parameter(parameter="sortsParam", name="sorts", in="query", required=false, example="-id", description="Fields for sorting separated by commas. Use '-' to sort descending", @OA\Schema(type="string")),
 * @OA\Parameter(parameter="perPageParam", name="per_page", in="query", required=false, example=15, description="Items per page", @OA\Schema(type="integer")),
 * @OA\Parameter(parameter="pageParam", name="page", in="query", required=false, example=1, description="Page number", @OA\Schema(type="integer")),
 *
 * @OA\Schema(
 *     schema="LookupRequest",
 *     type="object",
 *
 *     @OA\Property(property="q", type="string", example="", description="Free text search across the searchable columns"),
 *     @OA\Property(property="filters", ref="#/components/schemas/Filters", example={"name": {"like": "São Paulo"}}),
 *     @OA\Property(property="keys", type="array", example={"1","3"}, @OA\Items(type="string"), description="Values of the lookup key column to filter by"),
 *     @OA\Property(property="sorts", type="string", example="-id", description="Fields for sorting separated by commas. Use '-' to sort descending"),
 *     @OA\Property(property="per_page", type="integer", example=15, description="Items per page"),
 *     @OA\Property(property="page", type="integer", example=1, description="Page number")
 * )
 */
class LookupRequest extends ListRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'keys' => 'nullable|array',
        ];
    }
}
