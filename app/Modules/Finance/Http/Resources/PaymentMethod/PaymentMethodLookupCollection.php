<?php

namespace App\Modules\Finance\Http\Resources\PaymentMethod;

use App\Core\Helpers\PaginatorHelpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *     schema="PaymentMethodLookupCollection",
 *     type="object",
 *     allOf={
 *         @OA\Schema(
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PaymentMethodLookupResource")),
 *         ),
 *         @OA\Schema(ref="#/components/schemas/PaginatorInfo"),
 *     }
 * )
 */
class PaymentMethodLookupCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        $info = PaginatorHelpers::getInfoFromPaginator($this->resource)->toArray();

        return [
            'data' => PaymentMethodLookupResource::collection($this->collection),
            ...$info
        ];
    }
}