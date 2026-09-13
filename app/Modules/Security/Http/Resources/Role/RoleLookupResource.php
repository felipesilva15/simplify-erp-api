<?php

namespace App\Modules\Security\Http\Resources\Role;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="RoleLookupResource",
 *      @OA\Property(property="key", type="integer", example=1),
 *      @OA\Property(property="label", type="string", example="Financeiro", minLength=1, maxLength=80),
 *      @OA\Property(property="sublabel", type="string", example="Cod.: 1", minLength=1, maxLength=80),
 *      @OA\Property(
 *          property="meta",
 *          type="object",
 *          @OA\Property(property="id", type="integer", example=1),
 *          @OA\Property(property="name", type="string", example="Financeiro", minLength=1, maxLength=80)
 *      )
 * )
 */
class RoleLookupResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'key' => $this->id,
            'label' => $this->name,
            'sublabel' => 'Cod.: '.$this->id,
            'meta' => $this->only('id', 'name')
        ];
    }
}