<?php

namespace App\Modules\Construction\Http\Resources\ProjectType;

use App\Core\Helpers\PaginatorHelpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *     schema="ProjectTypeCollection",
 *     type="object",
 *     allOf={
 *         @OA\Schema(
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ProjectTypeResource")),
 *         ),
 *         @OA\Schema(ref="#/components/schemas/PaginatorInfo"),
 *     }
 * )
 */
class ProjectTypeCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        $info = PaginatorHelpers::getInfoFromPaginator($this->resource)->toArray();

        return [
            'data' => ProjectTypeResource::collection($this->collection),
            ...$info
        ];
    }
}
