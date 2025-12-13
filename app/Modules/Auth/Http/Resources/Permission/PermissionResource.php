<?php

namespace App\Modules\Auth\Http\Resources\Permission;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="PermissionResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="resource", type="string", example="Sample", minLength=1, maxLength=60),
 *      @OA\Property(property="action", type="string", example="Sample", minLength=1, maxLength=120),
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=180),
 *      @OA\Property(property="description", type="string", example="Sample", minLength=1, maxLength=512, nullable=true),
 *      @OA\Property(property="has_access_free", type="boolean", example=false, nullable=true),
 *      @OA\Property(property="is_active", type="boolean", example=false, nullable=true),
 *      @OA\Property(property="module", type="object", ref="#/components/schemas/PermissionModuleResource"),
 *      @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-05T00:27:48.930766Z", nullable=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-05T00:27:48.930766Z", nullable=true),
 *      @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-05T00:27:48.930766Z", nullable=true)
 * )
 */
class PermissionResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'resource' => $this->getAttribute('resource'),
            'action' => $this->action,
            'name' => $this->name,
            'description' => $this->description,
            'has_access_free' => $this->has_access_free,
            'is_active' => $this->is_active,
            'module' => new PermissionModuleResource($this->module),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}