<?php

namespace App\Modules\Geography\Http\Resources\Address;

use App\Core\Helpers\PaginatorHelpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *     schema="AddressCollection",
 *     type="object",
 *     allOf={
 *         @OA\Schema(
 *
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AddressResource")),
 *         ),
 *
 *         @OA\Schema(ref="#/components/schemas/PaginatorInfo"),
 *     }
 * )
 */
class AddressCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        $info = PaginatorHelpers::getInfoFromPaginator($this->resource)->toArray();

        return [
            'data' => AddressResource::collection($this->collection),
            ...$info,
        ];
    }
}
