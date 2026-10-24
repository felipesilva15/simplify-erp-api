<?php

namespace App\Modules\Catalog\Http\Resources\MeasurementUnit;

use App\Core\Helpers\PaginatorHelpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *     schema="MeasurementUnitCollection",
 *     type="object",
 *     allOf={
 *         @OA\Schema(
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/MeasurementUnitResource")),
 *         ),
 *         @OA\Schema(ref="#/components/schemas/PaginatorInfo"),
 *     }
 * )
 */
class MeasurementUnitCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        $info = PaginatorHelpers::getInfoFromPaginator($this->resource)->toArray();

        return [
            'data' => MeasurementUnitResource::collection($this->collection),
            ...$info
        ];
    }
}
