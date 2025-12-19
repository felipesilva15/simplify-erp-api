<?php

namespace App\Modules\Security\Http\Resources\Role;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      schema="RolePermissionResource",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="resource", type="string", example="Sample", minLength=1, maxLength=60),
 *      @OA\Property(property="action", type="string", example="Sample", minLength=1, maxLength=120),
 *      @OA\Property(property="name", type="string", example="Sample", minLength=1, maxLength=180),
 *      @OA\Property(property="description", type="string", example="Sample", minLength=1, maxLength=512, nullable=true)
 * )
 */
class RolePermissionResource extends JsonResource
{
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'resource' => $this->getAttribute('resource'),
            'action' => $this->action,
            'name' => $this->name,
            'description' => $this->description
        ];
    }
}