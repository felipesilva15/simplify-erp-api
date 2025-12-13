<?php

namespace App\Modules\Auth\Http\Resources\Role;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="RolePermissionResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="group", type="string", example="Sample", minLength=1, maxLength=60),
 *      @OA\Property(property="action", type="string", example="Sample", minLength=1, maxLength=120),
 *      @OA\Property(property="description", type="string", example="Sample", minLength=1, maxLength=512, nullable=true),
 *      @OA\Property(property="has_access_free", type="boolean", example=false, nullable=true),
 *      @OA\Property(property="is_active", type="boolean", example=false, nullable=true)
 * )
 */
class RolePermissionResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'group' => $this->group,
            'action' => $this->action,
            'description' => $this->description,
            'has_access_free' => $this->has_access_free,
            'is_active' => $this->is_active,
        ];
    }
}