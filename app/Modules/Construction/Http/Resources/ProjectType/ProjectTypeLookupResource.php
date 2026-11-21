<?php

namespace App\Modules\Construction\Http\Resources\ProjectType;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="ProjectTypeLookupResource",
 *      @OA\Property(property="key", type="integer", example=1),
 *      @OA\Property(property="label", type="string", example="Reforma", minLength=1, maxLength=80),
 *      @OA\Property(property="sublabel", type="string", example="Cód.: 1", minLength=1, maxLength=80),
 *      @OA\Property(
 *          property="meta",
 *          type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Reforma", minLength=1, maxLength=80)
 *      )
 * )
 */
class ProjectTypeLookupResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'key' => $this->id,
            'label' => $this->name,
            'sublabel' => "Cód.: {$this->id}",
            'meta' => $this->only('id', 'name')
        ];
    }
}