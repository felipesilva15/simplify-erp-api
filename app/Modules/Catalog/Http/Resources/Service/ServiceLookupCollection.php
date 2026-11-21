<?php

namespace App\Modules\Catalog\Http\Resources\Service;

use App\Core\Helpers\PaginatorHelpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *     schema="ServiceLookupCollection",
 *     type="object",
 *     allOf={
 *         @OA\Schema(
 *
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ServiceLookupResource")),
 *         ),
 *
 *         @OA\Schema(ref="#/components/schemas/PaginatorInfo"),
 *     }
 * )
 */
class ServiceLookupCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        $info = PaginatorHelpers::getInfoFromPaginator($this->resource)->toArray();

        return [
            'data' => ServiceLookupResource::collection($this->collection),
            ...$info,
        ];
    }
}
