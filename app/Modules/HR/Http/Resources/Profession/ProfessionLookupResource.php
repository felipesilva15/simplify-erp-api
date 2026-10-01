<?php

namespace App\Modules\HR\Http\Resources\Profession;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="ProfessionLookupResource",
 *      @OA\Property(property="key", type="integer", example=1),
 *      @OA\Property(property="label", type="string", example="Sample", minLength=1, maxLength=180),
 *      @OA\Property(property="sublabel", type="string", example="Cod.: 1 | CBO: 123456", minLength=1, maxLength=80),
 *      @OA\Property(
 *          property="meta",
 *          type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=180),
 *         @OA\Property(property="cbo", type="string", example="Sample", minLength=1, maxLength=6)
 *      )
 * )
 */
class ProfessionLookupResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'key' => $this->id,
            'label' => $this->name,
            'sublabel' => "Cod.: {$this->id} | CBO: {$this->cbo}",
            'meta' => $this->only('id', 'name', 'cbo')
        ];
    }
}