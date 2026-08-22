<?php

namespace App\Core\Http\Resources\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="ResourceResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Sample module", minLength=1, maxLength=80),
 *      @OA\Property(property="slug", type="string", example="sample", minLength=1, maxLength=80),
 *      @OA\Property(property="description", type="string", example="Sample", minLength=1, maxLength=512, nullable=true),
 *      @OA\Property(property="is_active", type="boolean", example=false, nullable=true),
 *      @OA\Property(property="permissions", type="array", @OA\Items(ref="#/components/schemas/ResourcePermissionResource")),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-05T00:30:46.143219Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-05T00:30:46.143219Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-05T00:30:46.143219Z", nullable=true)
 * )
 */
class ResourceResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'module' => new ResourceModuleResource($this->module),
            'permissions' => ResourcePermissionResource::collection($this->permissions),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}