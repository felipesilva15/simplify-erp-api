<?php

namespace App\Modules\Catalog\Http\Resources\MeasurementUnit;

use App\Core\Helpers\PaginatorHelpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *     schema="MeasurementUnitLookupCollection",
 *     type="object",
 *     allOf={
 *         @OA\Schema(
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MeasurementUnitLookupResource")),
 *         ),
 *         @OA\Schema(ref="#/components/schemas/PaginatorInfo"),
 *     }
 * )
 */
class MeasurementUnitLookupCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        $info = PaginatorHelpers::getInfoFromPaginator($this->resource)->toArray();

        return [
            'data' => MeasurementUnitLookupResource::collection($this->collection),
            ...$info
        ];
    }
}