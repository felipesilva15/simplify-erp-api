<?php

namespace App\Modules\ThirdParty\Http\Resources\Partner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="PartnerProfessionResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="cbo", type="string", example="Sample", minLength=1, maxLength=6),
 *     @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=180),
 * )
 */
class PartnerProfessionResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'cbo' => $this->cbo,
            'name' => $this->name
        ];
    }
}