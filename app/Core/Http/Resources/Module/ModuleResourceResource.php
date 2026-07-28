<?php

namespace App\Core\Http\Resources\Module;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="ModuleResourceResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="resource", type="string", example="Sample module", minLength=1, maxLength=60),
 *      @OA\Property(property="action", type="string", example="sample", minLength=1, maxLength=120),
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=180),
 *      @OA\Property(property="description", type="string", example="Sample", minLength=1, maxLength=512, nullable=true),
 * )
 */
class ModuleResourceResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'permissions' => ModuleResourcePermissionResource::collection($this->permissions)
        ];
    }
}