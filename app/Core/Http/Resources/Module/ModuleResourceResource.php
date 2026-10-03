<?php

namespace App\Core\Http\Resources\Module;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="ModuleResourceResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=80),
 *      @OA\Property(property="label", type="string", example="Sample", minLength=1, maxLength=80),
 *      @OA\Property(property="slug", type="string", example="Sample", minLength=1, maxLength=80),
 *      @OA\Property(property="description", type="string", example="Sample", minLength=1, maxLength=512, nullable=true),
 * )
 */
class ModuleResourceResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'permissions' => ModuleResourcePermissionResource::collection($this->permissions)
        ];
    }
}