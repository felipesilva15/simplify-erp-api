<?php

namespace App\Core\Http\Resources\Module;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="ModuleResourcePermissionResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="sample", minLength=1, maxLength=120),
 *      @OA\Property(property="label", type="string", example="Sample", minLength=1, maxLength=180),
 *      @OA\Property(property="description", type="string", example="Sample", minLength=1, maxLength=512, nullable=true),
 *      @OA\Property(property="action", type="string", example="Sample", maxLength=80, nullable=true)
 * )
 */
class ModuleResourcePermissionResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->label,
            'description' => $this->description,
            'action' => $this->action
        ];
    }
}