<?php

namespace App\Modules\Catalog\Http\Resources\Service;

use App\Core\Helpers\PaginatorHelpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *     schema="ServiceCollection",
 *     type="object",
 *     allOf={
 *         @OA\Schema(
 *
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ServiceResource")),
 *         ),
 *
 *         @OA\Schema(ref="#/components/schemas/PaginatorInfo"),
 *     }
 * )
 */
class ServiceCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        $info = PaginatorHelpers::getInfoFromPaginator($this->resource)->toArray();

        return [
            'data' => ServiceResource::collection($this->collection),
            ...$info,
        ];
    }
}
