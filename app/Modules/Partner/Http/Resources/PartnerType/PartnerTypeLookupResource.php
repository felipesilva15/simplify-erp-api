<?php

namespace App\Modules\Partner\Http\Resources\PartnerType;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="PartnerTypeLookupResource",
 *      @OA\Property(property="key", type="string", example="TYP"),
 *      @OA\Property(property="label", type="string", example="Sample", minLength=1, maxLength=80),
 *      @OA\Property(property="sublabel", type="string", example="Cod.: 1", minLength=1, maxLength=80),
 *      @OA\Property(property="meta", type="object"),
 * )
 */
class PartnerTypeLookupResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'key' => $this->code,
            'label' => $this->name,
            'sublabel' => 'Cod.: '.$this->code,
            'meta' => $this->only('id', 'name', 'code')
        ];
    }
}